<?php

declare(strict_types=1);

namespace PhpMyAdmin\Tests\Controllers\Server\Databases;

use PhpMyAdmin\Config;
use PhpMyAdmin\Controllers\Server\Databases\CreateController;
use PhpMyAdmin\DatabaseInterface;
use PhpMyAdmin\Http\ServerRequest;
use PhpMyAdmin\Template;
use PhpMyAdmin\Tests\AbstractTestCase;
use PhpMyAdmin\Tests\Stubs\DbiDummy;
use PhpMyAdmin\Tests\Stubs\ResponseRenderer;
use PHPUnit\Framework\Attributes\CoversClass;

use function __;
use function sprintf;

#[CoversClass(CreateController::class)]
final class CreateControllerTest extends AbstractTestCase
{
    protected DatabaseInterface $dbi;

    protected DbiDummy $dummyDbi;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dummyDbi = $this->createDbiDummy();
        $this->dbi = $this->createDatabaseInterface($this->dummyDbi);
        DatabaseInterface::$instance = $this->dbi;
    }

    public function testCreateDatabase(): void
    {
        $GLOBALS['server'] = 1;
        $GLOBALS['text_dir'] = 'ltr';
        Config::getInstance()->selectedServer['DisableIS'] = false;
        $GLOBALS['db'] = 'pma_test';
        $GLOBALS['table'] = '';

        $response = new ResponseRenderer();

        $template = new Template();
        $controller = new CreateController($response, $template, $this->dbi);

        $request = $this->createStub(ServerRequest::class);
        $request->method('getParsedBodyParam')->willReturnMap([
            ['new_db', null, 'test_db_error'],
            ['db_collation', null, null],
        ]);

        $controller($request);
        $actual = $response->getJSONResult();

        $this->assertArrayHasKey('message', $actual);
        $this->assertStringContainsString('<div class="alert alert-danger" role="alert">', $actual['message']);

        $response = new ResponseRenderer();

        $controller = new CreateController($response, $template, $this->dbi);

        $request = $this->createStub(ServerRequest::class);
        $request->method('isAjax')->willReturn(true);
        $request->method('getParsedBodyParam')->willReturnMap([
            ['new_db', null, 'test_db'],
            ['db_collation', null, 'utf8_general_ci'],
        ]);

        $controller($request);
        $actual = $response->getJSONResult();

        $this->assertArrayHasKey('message', $actual);
        $this->assertStringContainsString('<div class="alert alert-success" role="alert">', $actual['message']);
        $this->assertStringContainsString(
            sprintf(__('Database %1$s has been created.'), 'test_db'),
            $actual['message'],
        );
    }
}
