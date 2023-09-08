<?php

declare(strict_types=1);

namespace PhpMyAdmin\Controllers\Server;

use Fig\Http\Message\StatusCodeInterface;
use PhpMyAdmin\CheckUserPrivileges;
use PhpMyAdmin\ConfigStorage\Features\ConfigurableMenusFeature;
use PhpMyAdmin\ConfigStorage\Relation;
use PhpMyAdmin\Controllers\AbstractController;
use PhpMyAdmin\DatabaseInterface;
use PhpMyAdmin\Dbal\Connection;
use PhpMyAdmin\Http\ServerRequest;
use PhpMyAdmin\ResponseRenderer;
use PhpMyAdmin\Template;
use PhpMyAdmin\Util;

use function __;
use function sprintf;

final class UserGroupsFormController extends AbstractController
{
    public function __construct(
        ResponseRenderer $response,
        Template $template,
        private Relation $relation,
        private DatabaseInterface $dbi,
    ) {
        parent::__construct($response, $template);
    }

    public function __invoke(ServerRequest $request): void
    {
        if (! $request->isAjax()) {
            return;
        }

        /** @var string $username */
        $username = $request->getQueryParam('username', '');

        if ($username === '') {
            $this->response->setRequestStatus(false);
            $this->response->setStatusCode(StatusCodeInterface::STATUS_BAD_REQUEST);
            $this->response->addJSON('message', __('Missing parameter:') . ' username');

            return;
        }

        $checkUserPrivileges = new CheckUserPrivileges($this->dbi);
        $checkUserPrivileges->getPrivileges();

        $configurableMenusFeature = $this->relation->getRelationParameters()->configurableMenusFeature;
        if ($configurableMenusFeature === null) {
            $this->response->setRequestStatus(false);
            $this->response->setStatusCode(StatusCodeInterface::STATUS_BAD_REQUEST);
            $this->response->addJSON('message', __('User groups management is not enabled.'));

            return;
        }

        $form = $this->getHtmlToChooseUserGroup($username, $configurableMenusFeature);

        $this->response->addJSON('message', $form);
    }

    /**
     * Displays a dropdown to select the user group with menu items configured to each of them.
     */
    private function getHtmlToChooseUserGroup(
        string $username,
        ConfigurableMenusFeature $configurableMenusFeature,
    ): string {
        $groupTable = Util::backquote($configurableMenusFeature->database)
            . '.' . Util::backquote($configurableMenusFeature->userGroups);
        $userTable = Util::backquote($configurableMenusFeature->database)
            . '.' . Util::backquote($configurableMenusFeature->users);

        $sqlQuery = sprintf(
            'SELECT `usergroup` FROM %s WHERE `username` = %s',
            $userTable,
            $this->dbi->quoteString($username),
        );
        $userGroup = $this->dbi->fetchValue($sqlQuery, 0, Connection::TYPE_CONTROL);

        $allUserGroups = [];
        $sqlQuery = 'SELECT DISTINCT `usergroup` FROM ' . $groupTable;
        $result = $this->dbi->tryQueryAsControlUser($sqlQuery);
        if ($result) {
            while ($row = $result->fetchRow()) {
                $allUserGroups[$row[0]] = $row[0];
            }
        }

        return $this->template->render('server/privileges/choose_user_group', [
            'all_user_groups' => $allUserGroups,
            'user_group' => $userGroup,
            'params' => ['username' => $username],
        ]);
    }
}
