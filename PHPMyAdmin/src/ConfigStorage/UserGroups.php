<?php
/**
 * set of functions for user group handling
 */

declare(strict_types=1);

namespace PhpMyAdmin\ConfigStorage;

use PhpMyAdmin\ConfigStorage\Features\ConfigurableMenusFeature;
use PhpMyAdmin\DatabaseInterface;
use PhpMyAdmin\Dbal\Connection;
use PhpMyAdmin\Html\Generator;
use PhpMyAdmin\Template;
use PhpMyAdmin\Url;
use PhpMyAdmin\Util;

use function __;
use function array_keys;
use function htmlspecialchars;
use function implode;
use function in_array;
use function mb_substr;
use function sprintf;
use function str_starts_with;

/**
 * PhpMyAdmin\Server\UserGroups class
 */
class UserGroups
{
    /**
     * Return HTML to list the users belonging to a given user group
     *
     * @param string $userGroup user group name
     *
     * @return string HTML to list the users belonging to a given user group
     */
    public static function getHtmlForListingUsersofAGroup(
        ConfigurableMenusFeature $configurableMenusFeature,
        string $userGroup,
    ): string {
        $users = [];

        $userGroupSpecialChars = htmlspecialchars($userGroup);
        $usersTable = Util::backquote($configurableMenusFeature->database)
            . '.' . Util::backquote($configurableMenusFeature->users);
        $dbi = DatabaseInterface::getInstance();
        $sqlQuery = 'SELECT `username` FROM ' . $usersTable
            . ' WHERE `usergroup`=' . $dbi->quoteString($userGroup, Connection::TYPE_CONTROL);
        $result = $dbi->tryQueryAsControlUser($sqlQuery);
        if ($result) {
            $i = 0;
            while ($row = $result->fetchRow()) {
                $users[] = ['count' => ++$i, 'user' => $row[0]];
            }
        }

        $template = new Template();

        return $template->render('server/user_groups/user_listings', [
            'user_group_special_chars' => $userGroupSpecialChars,
            'users' => $users,
        ]);
    }

    /**
     * Returns HTML for the 'user groups' table
     *
     * @return string HTML for the 'user groups' table
     */
    public static function getHtmlForUserGroupsTable(ConfigurableMenusFeature $configurableMenusFeature): string
    {
        $groupTable = Util::backquote($configurableMenusFeature->database)
            . '.' . Util::backquote($configurableMenusFeature->userGroups);
        $sqlQuery = 'SELECT * FROM ' . $groupTable . ' ORDER BY `usergroup` ASC';
        $result = DatabaseInterface::getInstance()->tryQueryAsControlUser($sqlQuery);
        $userGroups = [];
        $userGroupsValues = [];
        $action = Url::getFromRoute('/server/privileges');
        $hiddenInputs = null;
        if ($result && $result->numRows()) {
            $hiddenInputs = Url::getHiddenInputs();
            foreach ($result as $row) {
                $groupName = $row['usergroup'];
                if (! isset($userGroups[$groupName])) {
                    $userGroups[$groupName] = [];
                }

                $userGroups[$groupName][$row['tab']] = $row['allowed'];
            }

            foreach ($userGroups as $groupName => $tabs) {
                $userGroupVal = [];
                $userGroupVal['name'] = $groupName;
                $userGroupVal['serverTab'] = self::getAllowedTabNames($tabs, 'server');
                $userGroupVal['dbTab'] = self::getAllowedTabNames($tabs, 'db');
                $userGroupVal['tableTab'] = self::getAllowedTabNames($tabs, 'table');
                $userGroupVal['userGroupUrl'] = Url::getFromRoute('/server/user-groups');
                $userGroupVal['viewUsersUrl'] = Url::getCommon(
                    ['viewUsers' => 1, 'userGroup' => $groupName],
                    '',
                    false,
                );
                $userGroupVal['viewUsersIcon'] = Generator::getIcon('b_usrlist', __('View users'));

                $userGroupVal['editUsersUrl'] = Url::getCommon(
                    ['editUserGroup' => 1, 'userGroup' => $groupName],
                    '',
                    false,
                );
                $userGroupVal['editUsersIcon'] = Generator::getIcon('b_edit', __('Edit'));
                $userGroupsValues[] = $userGroupVal;
            }
        }

        $addUserUrl = Url::getFromRoute('/server/user-groups', ['addUserGroup' => 1]);
        $addUserIcon = Generator::getIcon('b_usradd');
        $template = new Template();

        return $template->render('server/user_groups/user_groups', [
            'action' => $action,
            'hidden_inputs' => $hiddenInputs ?? '',
            'has_rows' => $userGroups !== [],
            'user_groups_values' => $userGroupsValues,
            'add_user_url' => $addUserUrl,
            'add_user_icon' => $addUserIcon,
        ]);
    }

    /**
     * Returns the list of allowed menu tab names
     * based on a data row from usergroup table.
     *
     * @param mixed[] $row   row of usergroup table
     * @param string  $level 'server', 'db' or 'table'
     *
     * @return string comma separated list of allowed menu tab names
     */
    public static function getAllowedTabNames(array $row, string $level): string
    {
        $tabNames = [];
        $tabs = Util::getMenuTabList($level);
        foreach ($tabs as $tab => $tabName) {
            if (isset($row[$level . '_' . $tab]) && $row[$level . '_' . $tab] !== 'Y') {
                continue;
            }

            $tabNames[] = $tabName;
        }

        return implode(', ', $tabNames);
    }

    /** @param non-empty-string $userGroupName */
    public static function delete(
        DatabaseInterface $dbi,
        ConfigurableMenusFeature $configurableMenusFeature,
        string $userGroupName,
    ): void {
        $statement = sprintf(
            'DELETE FROM %s.%s WHERE `usergroup`=%s',
            Util::backquote($configurableMenusFeature->database),
            Util::backquote($configurableMenusFeature->users),
            $dbi->quoteString($userGroupName, Connection::TYPE_CONTROL),
        );
        $dbi->queryAsControlUser($statement);

        $statement = sprintf(
            'DELETE FROM %s.%s WHERE `usergroup`=%s',
            Util::backquote($configurableMenusFeature->database),
            Util::backquote($configurableMenusFeature->userGroups),
            $dbi->quoteString($userGroupName, Connection::TYPE_CONTROL),
        );
        $dbi->queryAsControlUser($statement);
    }

    /**
     * Returns HTML for add/edit user group dialog
     *
     * @param string|null $userGroup name of the user group in case of editing
     *
     * @return string HTML for add/edit user group dialog
     */
    public static function getHtmlToEditUserGroup(
        ConfigurableMenusFeature $configurableMenusFeature,
        string|null $userGroup = null,
    ): string {
        $urlParams = [];

        $editUserGroupSpecialChars = '';
        if ($userGroup !== null) {
            $editUserGroupSpecialChars = htmlspecialchars($userGroup);
        }

        if ($userGroup !== null) {
            $urlParams['userGroup'] = $userGroup;
            $urlParams['editUserGroupSubmit'] = '1';
        } else {
            $urlParams['addUserGroupSubmit'] = '1';
        }

        $allowedTabs = ['server' => [], 'db' => [], 'table' => []];
        if ($userGroup !== null) {
            $groupTable = Util::backquote($configurableMenusFeature->database)
                . '.' . Util::backquote($configurableMenusFeature->userGroups);
            $dbi = DatabaseInterface::getInstance();
            $sqlQuery = 'SELECT * FROM ' . $groupTable
                . ' WHERE `usergroup`=' . $dbi->quoteString($userGroup, Connection::TYPE_CONTROL);
            $result = $dbi->tryQueryAsControlUser($sqlQuery);
            if ($result) {
                foreach ($result as $row) {
                    $key = $row['tab'];
                    $value = $row['allowed'];
                    if (str_starts_with($key, 'server_') && $value === 'Y') {
                        $allowedTabs['server'][] = mb_substr($key, 7);
                    } elseif (str_starts_with($key, 'db_') && $value === 'Y') {
                        $allowedTabs['db'][] = mb_substr($key, 3);
                    } elseif (str_starts_with($key, 'table_') && $value === 'Y') {
                        $allowedTabs['table'][] = mb_substr($key, 6);
                    }
                }
            }

            unset($result);
        }

        $tabList = self::getTabList(
            __('Server-level tabs'),
            'server',
            $allowedTabs['server'],
        );
        $tabList .= self::getTabList(
            __('Database-level tabs'),
            'db',
            $allowedTabs['db'],
        );
        $tabList .= self::getTabList(
            __('Table-level tabs'),
            'table',
            $allowedTabs['table'],
        );

        $template = new Template();

        return $template->render('server/user_groups/edit_user_groups', [
            'user_group' => $userGroup,
            'edit_user_group_special_chars' => $editUserGroupSpecialChars,
            'user_group_url' => Url::getFromRoute('/server/user-groups'),
            'hidden_inputs' => Url::getHiddenInputs($urlParams),
            'tab_list' => $tabList,
        ]);
    }

    /**
     * Returns HTML for checkbox groups to choose
     * tabs of 'server', 'db' or 'table' levels.
     *
     * @param string  $title    title of the checkbox group
     * @param string  $level    'server', 'db' or 'table'
     * @param mixed[] $selected array of selected allowed tabs
     *
     * @return string HTML for checkbox groups
     */
    public static function getTabList(string $title, string $level, array $selected): string
    {
        $tabs = Util::getMenuTabList($level);
        $tabDetails = [];
        foreach ($tabs as $tab => $tabName) {
            $tabDetail = [];
            $tabDetail['in_array'] = (in_array($tab, $selected) ? ' checked="checked"' : '');
            $tabDetail['tab'] = $tab;
            $tabDetail['tab_name'] = $tabName;
            $tabDetails[] = $tabDetail;
        }

        $template = new Template();

        return $template->render('server/user_groups/tab_list', [
            'title' => $title,
            'level' => $level,
            'tab_details' => $tabDetails,
        ]);
    }

    /**
     * Add/update a user group with allowed menu tabs.
     *
     * @param string $userGroup user group name
     * @param bool   $new       whether this is a new user group
     */
    public static function edit(
        ConfigurableMenusFeature $configurableMenusFeature,
        string $userGroup,
        bool $new = false,
    ): void {
        $tabs = Util::getMenuTabList();
        $groupTable = Util::backquote($configurableMenusFeature->database)
            . '.' . Util::backquote($configurableMenusFeature->userGroups);

        $dbi = DatabaseInterface::getInstance();
        if (! $new) {
            $sqlQuery = 'DELETE FROM ' . $groupTable
                . ' WHERE `usergroup`=' . $dbi->quoteString($userGroup, Connection::TYPE_CONTROL) . ';';
            $dbi->queryAsControlUser($sqlQuery);
        }

        $sqlQuery = 'INSERT INTO ' . $groupTable
            . '(`usergroup`, `tab`, `allowed`)'
            . ' VALUES ';
        $first = true;
        /** @var array<string, string> $tabGroup */
        foreach ($tabs as $tabGroupName => $tabGroup) {
            foreach (array_keys($tabGroup) as $tab) {
                if (! $first) {
                    $sqlQuery .= ', ';
                }

                $tabName = $tabGroupName . '_' . $tab;
                $allowed = isset($_POST[$tabName]) && $_POST[$tabName] === 'Y';
                $sqlQuery .= '(' . $dbi->quoteString($userGroup, Connection::TYPE_CONTROL)
                    . ', ' . $dbi->quoteString($tabName, Connection::TYPE_CONTROL) . ", '"
                    . ($allowed ? 'Y' : 'N') . "')";
                $first = false;
            }
        }

        $sqlQuery .= ';';
        $dbi->queryAsControlUser($sqlQuery);
    }
}
