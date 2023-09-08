<?php

declare(strict_types=1);

namespace PhpMyAdmin\Controllers\Database\Structure;

use PhpMyAdmin\Config;
use PhpMyAdmin\Controllers\AbstractController;
use PhpMyAdmin\DatabaseInterface;
use PhpMyAdmin\DbTableExists;
use PhpMyAdmin\Http\ServerRequest;
use PhpMyAdmin\Identifiers\DatabaseName;
use PhpMyAdmin\Message;
use PhpMyAdmin\ResponseRenderer;
use PhpMyAdmin\Template;
use PhpMyAdmin\Url;
use PhpMyAdmin\Util;

use function __;
use function json_encode;

/**
 * Handles request for real row count on database level view page.
 */
final class RealRowCountController extends AbstractController
{
    public function __construct(
        ResponseRenderer $response,
        Template $template,
        private DatabaseInterface $dbi,
        private readonly DbTableExists $dbTableExists,
    ) {
        parent::__construct($response, $template);
    }

    public function __invoke(ServerRequest $request): void
    {
        $GLOBALS['errorUrl'] ??= null;

        $parameters = [
            'real_row_count_all' => $_REQUEST['real_row_count_all'] ?? null,
            'table' => $_REQUEST['table'] ?? null,
        ];

        if (! $this->checkParameters(['db'])) {
            return;
        }

        $GLOBALS['errorUrl'] = Util::getScriptNameForOption(
            Config::getInstance()->settings['DefaultTabDatabase'],
            'database',
        );
        $GLOBALS['errorUrl'] .= Url::getCommon(['db' => $GLOBALS['db']], '&');

        if (! $request->isAjax()) {
            return;
        }

        $databaseName = DatabaseName::tryFrom($request->getParam('db'));
        if ($databaseName === null || ! $this->dbTableExists->hasDatabase($databaseName)) {
            $this->response->setRequestStatus(false);
            $this->response->addJSON('message', Message::error(__('No databases selected.')));

            return;
        }

        [$tables] = Util::getDbInfo($request, $GLOBALS['db']);

        // If there is a request to update all table's row count.
        if (! isset($parameters['real_row_count_all'])) {
            // Get the real row count for the table.
            $realRowCount = (int) $this->dbi
                ->getTable($GLOBALS['db'], (string) $parameters['table'])
                ->getRealRowCountTable();
            // Format the number.
            $realRowCount = Util::formatNumber($realRowCount, 0);

            $this->response->addJSON(['real_row_count' => $realRowCount]);

            return;
        }

        // Array to store the results.
        $realRowCountAll = [];
        // Iterate over each table and fetch real row count.
        foreach ($tables as $table) {
            $rowCount = $this->dbi
                ->getTable($GLOBALS['db'], $table['TABLE_NAME'])
                ->getRealRowCountTable();
            $realRowCountAll[] = ['table' => $table['TABLE_NAME'], 'row_count' => $rowCount];
        }

        $this->response->addJSON(['real_row_count_all' => json_encode($realRowCountAll)]);
    }
}
