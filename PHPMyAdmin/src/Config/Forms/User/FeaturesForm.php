<?php
/**
 * User preferences form
 */

declare(strict_types=1);

namespace PhpMyAdmin\Config\Forms\User;

use PhpMyAdmin\Config;
use PhpMyAdmin\Config\Forms\BaseForm;

use function __;

class FeaturesForm extends BaseForm
{
    /** @return mixed[] */
    public static function getForms(): array
    {
        $result = [
            'General' => [
                'VersionCheck',
                'NaturalOrder',
                'InitialSlidersState',
                'LoginCookieValidity',
                'SkipLockedTables',
                'DisableMultiTableMaintenance',
                'ShowHint',
                'SendErrorReports',
                'ConsoleEnterExecutes',
                'DisableShortcutKeys',
                'FirstDayOfCalendar',
            ],
            'Databases' => [
                'Servers/1/only_db', // saves to Server/only_db
                'Servers/1/hide_db', // saves to Server/hide_db
                'MaxDbList',
                'MaxTableList',
                'DefaultConnectionCollation',
            ],
            'Text_fields' => [
                'CharEditing',
                'MinSizeForInputField',
                'MaxSizeForInputField',
                'CharTextareaCols',
                'CharTextareaRows',
                'TextareaCols',
                'TextareaRows',
                'LongtextDoubleTextarea',
            ],
            'Page_titles' => ['TitleDefault', 'TitleTable', 'TitleDatabase', 'TitleServer'],
            'Warnings' => [
                'PmaNoRelation_DisableWarning',
                'SuhosinDisableWarning',
                'LoginCookieValidityDisableWarning',
                'ReservedWordDisableWarning',
            ],
            'Console' => [
                'Console/Mode',
                'Console/StartHistory',
                'Console/AlwaysExpand',
                'Console/CurrentQuery',
                'Console/EnterExecutes',
                'Console/DarkTheme',
                'Console/Height',
                'Console/GroupQueries',
                'Console/OrderBy',
                'Console/Order',
            ],
        ];
        // skip Developer form if no setting is available
        if (Config::getInstance()->settings['UserprefsDeveloperTab']) {
            $result['Developer'] = ['DBG/sql'];
        }

        return $result;
    }

    public static function getName(): string
    {
        return __('Features');
    }
}
