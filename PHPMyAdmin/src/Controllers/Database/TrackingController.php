<?php

declare(strict_types=1);

namespace PhpMyAdmin\Controllers\Database;

use PhpMyAdmin\CheckUserPrivileges;
use PhpMyAdmin\Config;
use PhpMyAdmin\Controllers\AbstractController;
use PhpMyAdmin\DatabaseInterface;
use PhpMyAdmin\DbTableExists;
use PhpMyAdmin\Html\Generator;
use PhpMyAdmin\Http\ServerRequest;
use PhpMyAdmin\Identifiers\DatabaseName;
use PhpMyAdmin\Message;
use PhpMyAdmin\Query\Utilities;
use PhpMyAdmin\ResponseRenderer;
use PhpMyAdmin\Template;
use PhpMyAdmin\Tracking\Tracking;
use PhpMyAdmin\Url;
use PhpMyAdmin\Util;

use function __;
use function htmlspecialchars;
use function sprintf;

/**
 * Tracking configuration for database.
 */
class TrackingController extends AbstractController
{
    public function __construct(
        ResponseRenderer $response,
        Template $template,
        private Tracking $tracking,
        private DatabaseInterface $dbi,
        private readonly DbTableExists $dbTableExists,
    ) {
        parent::__construct($response, $template);
    }

    public function __invoke(ServerRequest $request): void
    {
        $GLOBALS['text_dir'] ??= null;
        $GLOBALS['urlParams'] ??= null;
        $GLOBALS['errorUrl'] ??= null;

        $this->addScriptFiles(['vendor/jquery/jquery.tablesorter.js', 'database/tracking.js']);

        if (! $this->checkParameters(['db'])) {
            return;
        }

        $config = Config::getInstance();
        $GLOBALS['errorUrl'] = Util::getScriptNameForOption($config->settings['DefaultTabDatabase'], 'database');
        $GLOBALS['errorUrl'] .= Url::getCommon(['db' => $GLOBALS['db']], '&');

        $databaseName = DatabaseName::tryFrom($request->getParam('db'));
        if ($databaseName === null || ! $this->dbTableExists->hasDatabase($databaseName)) {
            if ($request->isAjax()) {
                $this->response->setRequestStatus(false);
                $this->response->addJSON('message', Message::error(__('No databases selected.')));

                return;
            }

            $this->redirect('/', ['reload' => true, 'message' => __('No databases selected.')]);

            return;
        }

        $GLOBALS['urlParams']['goto'] = Url::getFromRoute('/table/tracking');
        $GLOBALS['urlParams']['back'] = Url::getFromRoute('/database/tracking');

        [, $numTables] = Util::getDbInfo($request, $GLOBALS['db']);
        $isSystemSchema = Utilities::isSystemSchema($GLOBALS['db']);

        if ($request->hasBodyParam('delete_tracking') && $request->hasBodyParam('table')) {
            $this->tracking->deleteTracking($GLOBALS['db'], $request->getParsedBodyParam('table'));
            $this->response->addHTML(Message::success(
                __('Tracking data deleted successfully.'),
            )->getDisplay());
        } elseif ($request->hasBodyParam('submit_create_version')) {
            $this->tracking->createTrackingForMultipleTables(
                $GLOBALS['db'],
                $request->getParsedBodyParam('selected'),
                $request->getParsedBodyParam('version'),
            );
            $this->response->addHTML(Message::success(
                sprintf(
                    __(
                        'Version %1$s was created for selected tables, tracking is active for them.',
                    ),
                    htmlspecialchars($request->getParsedBodyParam('version')),
                ),
            )->getDisplay());
        } elseif ($request->hasBodyParam('submit_mult')) {
            $selectedTable = $request->getParsedBodyParam('selected_tbl');
            if (! empty($selectedTable)) {
                if ($request->getParsedBodyParam('submit_mult') === 'delete_tracking') {
                    foreach ($selectedTable as $table) {
                        $this->tracking->deleteTracking($GLOBALS['db'], $table);
                    }

                    $this->response->addHTML(Message::success(
                        __('Tracking data deleted successfully.'),
                    )->getDisplay());
                } elseif ($request->getParsedBodyParam('submit_mult') === 'track') {
                    $this->render('create_tracking_version', [
                        'route' => '/database/tracking',
                        'url_params' => $GLOBALS['urlParams'],
                        'last_version' => 0,
                        'db' => $GLOBALS['db'],
                        'selected' => $selectedTable,
                        'type' => 'both',
                        'default_statements' => $config->selectedServer['tracking_default_statements'],
                    ]);

                    return;
                }
            } else {
                $this->response->addHTML(Message::notice(
                    __('No tables selected.'),
                )->getDisplay());
            }
        }

        // Get tracked data about the database
        $trackedData = $this->tracking->getTrackedData($GLOBALS['db'], '', '1');

        // No tables present and no log exist
        if ($numTables === 0 && $trackedData->ddlog === []) {
            $this->response->addHTML('<p>' . __('No tables found in database.') . '</p>' . "\n");

            if (! $isSystemSchema) {
                $checkUserPrivileges = new CheckUserPrivileges($this->dbi);
                $checkUserPrivileges->getPrivileges();

                $this->render('database/create_table', ['db' => $GLOBALS['db']]);
            }

            return;
        }

        $this->response->addHTML(
            $this->tracking->getHtmlForDbTrackingTables($GLOBALS['db'], $GLOBALS['urlParams'], $GLOBALS['text_dir']),
        );

        // If available print out database log
        if ($trackedData->ddlog === []) {
            return;
        }

        $log = '';
        foreach ($trackedData->ddlog as $entry) {
            $log .= '# ' . $entry['date'] . ' ' . $entry['username'] . "\n"
                . $entry['statement'] . "\n";
        }

        $this->response->addHTML(Generator::getMessage(__('Database Log'), $log));
    }
}
