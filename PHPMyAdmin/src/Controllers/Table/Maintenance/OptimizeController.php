<?php

declare(strict_types=1);

namespace PhpMyAdmin\Controllers\Table\Maintenance;

use PhpMyAdmin\Config;
use PhpMyAdmin\Controllers\AbstractController;
use PhpMyAdmin\Html\Generator;
use PhpMyAdmin\Http\ServerRequest;
use PhpMyAdmin\Identifiers\DatabaseName;
use PhpMyAdmin\Identifiers\InvalidIdentifier;
use PhpMyAdmin\Identifiers\TableName;
use PhpMyAdmin\Message;
use PhpMyAdmin\ResponseRenderer;
use PhpMyAdmin\Table\Maintenance;
use PhpMyAdmin\Template;
use Webmozart\Assert\Assert;
use Webmozart\Assert\InvalidArgumentException;

use function __;
use function count;

final class OptimizeController extends AbstractController
{
    public function __construct(
        ResponseRenderer $response,
        Template $template,
        private Maintenance $model,
        private Config $config,
    ) {
        parent::__construct($response, $template);
    }

    public function __invoke(ServerRequest $request): void
    {
        $selectedTablesParam = $request->getParsedBodyParam('selected_tbl');

        try {
            Assert::isArray($selectedTablesParam);
            Assert::notEmpty($selectedTablesParam);
            Assert::allStringNotEmpty($selectedTablesParam);
        } catch (InvalidArgumentException) {
            $this->response->setRequestStatus(false);
            $this->response->addJSON('message', __('No table selected.'));

            return;
        }

        try {
            $database = DatabaseName::from($request->getParam('db'));
            $selectedTables = [];
            foreach ($selectedTablesParam as $table) {
                $selectedTables[] = TableName::from($table);
            }
        } catch (InvalidIdentifier $exception) {
            $message = Message::error($exception->getMessage());
            $this->response->setRequestStatus(false);
            $this->response->addJSON('message', $message->getDisplay());

            return;
        }

        if ($this->config->get('DisableMultiTableMaintenance') && count($selectedTables) > 1) {
            $this->response->setRequestStatus(false);
            $this->response->addJSON('message', __('Maintenance operations on multiple tables are disabled.'));

            return;
        }

        [$rows, $query] = $this->model->getOptimizeTableRows($database, $selectedTables);

        $message = Generator::getMessage(
            __('Your SQL query has been executed successfully.'),
            $query,
            'success',
        );

        $this->render('table/maintenance/optimize', ['message' => $message, 'rows' => $rows]);
    }
}
