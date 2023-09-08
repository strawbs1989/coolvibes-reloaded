<?php

declare(strict_types=1);

namespace PhpMyAdmin\Tests\Controllers\Table;

use PhpMyAdmin\Config;
use PhpMyAdmin\Controllers\Table\PrivilegesController;
use PhpMyAdmin\DatabaseInterface;
use PhpMyAdmin\Http\ServerRequest;
use PhpMyAdmin\Server\Privileges;
use PhpMyAdmin\Template;
use PhpMyAdmin\Tests\AbstractTestCase;
use PhpMyAdmin\Tests\Stubs\ResponseRenderer;
use PhpMyAdmin\Url;
use PHPUnit\Framework\Attributes\CoversClass;

use function __;

#[CoversClass(PrivilegesController::class)]
class PrivilegesControllerTest extends AbstractTestCase
{
    /**
     * Configures global environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        parent::setLanguage();

        parent::setTheme();

        DatabaseInterface::$instance = $this->createDatabaseInterface();
    }

    public function testIndex(): void
    {
        $GLOBALS['db'] = 'db';
        $GLOBALS['table'] = 'table';
        $GLOBALS['server'] = 0;
        Config::getInstance()->selectedServer['DisableIS'] = false;

        $privileges = [];

        $serverPrivileges = $this->createMock(Privileges::class);
        $serverPrivileges->method('getAllPrivileges')
            ->willReturn($privileges);

        $request = $this->createStub(ServerRequest::class);
        $request->method('getParam')->willReturnMap([['db', null, 'db'], ['table', null, 'table']]);

        $response = new ResponseRenderer();
        (new PrivilegesController(
            $response,
            new Template(),
            $serverPrivileges,
            DatabaseInterface::getInstance(),
        ))($request);
        $actual = $response->getHTMLResult();

        $this->assertStringContainsString($GLOBALS['db'] . '.' . $GLOBALS['table'], $actual);

        //validate 2: Url::getCommon
        $item = Url::getCommon(['db' => $GLOBALS['db'], 'table' => $GLOBALS['table']], '');
        $this->assertStringContainsString($item, $actual);

        //validate 3: items
        $this->assertStringContainsString(
            __('User'),
            $actual,
        );
        $this->assertStringContainsString(
            __('Host'),
            $actual,
        );
        $this->assertStringContainsString(
            __('Type'),
            $actual,
        );
        $this->assertStringContainsString(
            __('Privileges'),
            $actual,
        );
        $this->assertStringContainsString(
            __('Grant'),
            $actual,
        );
        $this->assertStringContainsString(
            __('Action'),
            $actual,
        );
        $this->assertStringContainsString(
            __('No user found'),
            $actual,
        );
    }

    public function testWithInvalidDatabaseName(): void
    {
        $request = $this->createStub(ServerRequest::class);
        $request->method('getParam')->willReturnMap([['db', null, ''], ['table', null, 'table']]);

        $response = new ResponseRenderer();
        (new PrivilegesController(
            $response,
            new Template(),
            $this->createStub(Privileges::class),
            $this->createDatabaseInterface(),
        ))($request);
        $actual = $response->getHTMLResult();

        $this->assertStringContainsString('<div class="alert alert-danger" role="alert">', $actual);
        $this->assertStringContainsString('The database name must be a non-empty string.', $actual);
    }

    public function testWithInvalidTableName(): void
    {
        $request = $this->createStub(ServerRequest::class);
        $request->method('getParam')->willReturnMap([['db', null, 'db'], ['table', null, '']]);

        $response = new ResponseRenderer();
        (new PrivilegesController(
            $response,
            new Template(),
            $this->createStub(Privileges::class),
            $this->createDatabaseInterface(),
        ))($request);
        $actual = $response->getHTMLResult();

        $this->assertStringContainsString('<div class="alert alert-danger" role="alert">', $actual);
        $this->assertStringContainsString('The table name must be a non-empty string.', $actual);
    }
}
