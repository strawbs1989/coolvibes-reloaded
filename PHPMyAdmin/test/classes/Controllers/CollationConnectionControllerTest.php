<?php

declare(strict_types=1);

namespace PhpMyAdmin\Tests\Controllers;

use PhpMyAdmin\Config;
use PhpMyAdmin\Controllers\CollationConnectionController;
use PhpMyAdmin\Http\ServerRequest;
use PhpMyAdmin\ResponseRenderer;
use PhpMyAdmin\Template;
use PhpMyAdmin\Tests\AbstractTestCase;
use PhpMyAdmin\Url;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(CollationConnectionController::class)]
class CollationConnectionControllerTest extends AbstractTestCase
{
    public function testInvoke(): void
    {
        $request = $this->createStub(ServerRequest::class);
        $request->method('getParsedBodyParam')->willReturnMap([['collation_connection', null, 'utf8mb4_general_ci']]);

        $response = $this->createMock(ResponseRenderer::class);
        $response->expects($this->once())->method('redirect')
            ->with('index.php?route=/' . Url::getCommonRaw([], '&'));

        $config = $this->createMock(Config::class);
        $config->expects($this->once())->method('setUserValue')
            ->with(null, 'DefaultConnectionCollation', 'utf8mb4_general_ci', 'utf8mb4_unicode_ci');

        (new CollationConnectionController(
            $response,
            new Template(),
            $config,
        ))($request);
    }
}
