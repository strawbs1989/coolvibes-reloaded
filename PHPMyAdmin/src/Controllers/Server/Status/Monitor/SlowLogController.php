<?php

declare(strict_types=1);

namespace PhpMyAdmin\Controllers\Server\Status\Monitor;

use PhpMyAdmin\Controllers\Server\Status\AbstractController;
use PhpMyAdmin\DatabaseInterface;
use PhpMyAdmin\Http\ServerRequest;
use PhpMyAdmin\ResponseRenderer;
use PhpMyAdmin\Server\Status\Data;
use PhpMyAdmin\Server\Status\Monitor;
use PhpMyAdmin\Template;
use PhpMyAdmin\Url;

final class SlowLogController extends AbstractController
{
    public function __construct(
        ResponseRenderer $response,
        Template $template,
        Data $data,
        private Monitor $monitor,
        private DatabaseInterface $dbi,
    ) {
        parent::__construct($response, $template, $data);
    }

    public function __invoke(ServerRequest $request): void
    {
        $GLOBALS['errorUrl'] ??= null;

        $GLOBALS['errorUrl'] = Url::getFromRoute('/');

        if ($this->dbi->isSuperUser()) {
            $this->dbi->selectDb('mysql');
        }

        if (! $request->isAjax()) {
            return;
        }

        $data = $this->monitor->getJsonForLogDataTypeSlow(
            (int) $request->getParsedBodyParam('time_start'),
            (int) $request->getParsedBodyParam('time_end'),
        );
        if ($data === null) {
            $this->response->setRequestStatus(false);

            return;
        }

        $this->response->addJSON(['message' => $data]);
    }
}
