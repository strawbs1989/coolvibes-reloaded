<?php

declare(strict_types=1);

namespace PhpMyAdmin\Controllers\Server\Variables;

use PhpMyAdmin\Controllers\AbstractController;
use PhpMyAdmin\DatabaseInterface;
use PhpMyAdmin\Http\ServerRequest;
use PhpMyAdmin\Providers\ServerVariables\ServerVariablesProvider;
use PhpMyAdmin\ResponseRenderer;
use PhpMyAdmin\Template;
use PhpMyAdmin\Util;

use function implode;
use function is_array;
use function is_string;

final class GetVariableController extends AbstractController
{
    public function __construct(ResponseRenderer $response, Template $template, private DatabaseInterface $dbi)
    {
        parent::__construct($response, $template);
    }

    public function __invoke(ServerRequest $request): void
    {
        if (! $request->isAjax()) {
            return;
        }

        $name = $this->getName($request->getAttribute('routeVars'));

        // Send with correct charset
        $this->response->addHeader('Content-Type', 'text/html; charset=UTF-8');
        $varValue = $this->dbi->fetchSingleRow(
            'SHOW GLOBAL VARIABLES WHERE Variable_name='
            . $this->dbi->quoteString($name) . ';',
            DatabaseInterface::FETCH_NUM,
        );

        $json = ['message' => $varValue[1]];

        $variableType = ServerVariablesProvider::getImplementation()->getVariableType($name);

        if ($variableType === 'byte') {
            /** @var string[] $bytes */
            $bytes = Util::formatByteDown($varValue[1], 3, 3);
            $json['message'] = implode(' ', $bytes);
        }

        $this->response->addJSON($json);
    }

    private function getName(mixed $routeVars): string
    {
        if (is_array($routeVars) && isset($routeVars['name']) && is_string($routeVars['name'])) {
            return $routeVars['name'];
        }

        return '';
    }
}
