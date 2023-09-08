<?php

declare(strict_types=1);

namespace PhpMyAdmin\Tests\Controllers\Server\Privileges;

use PhpMyAdmin\Controllers\Server\Privileges\AccountLockController;
use PhpMyAdmin\DatabaseInterface;
use PhpMyAdmin\Http\ServerRequest;
use PhpMyAdmin\Message;
use PhpMyAdmin\Server\Privileges\AccountLocking;
use PhpMyAdmin\Template;
use PhpMyAdmin\Tests\AbstractTestCase;
use PhpMyAdmin\Tests\Stubs\DummyResult;
use PhpMyAdmin\Tests\Stubs\ResponseRenderer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;

#[CoversClass(AccountLockController::class)]
class AccountLockControllerTest extends AbstractTestCase
{
    /** @var DatabaseInterface&Stub */
    private DatabaseInterface $dbiStub;

    /** @var ServerRequest&Stub  */
    private ServerRequest $requestStub;

    private ResponseRenderer $responseRendererStub;

    private AccountLockController $controller;

    protected function setUp(): void
    {
        parent::setUp();

        DatabaseInterface::$instance = $this->createDatabaseInterface();

        $GLOBALS['server'] = 1;
        $GLOBALS['text_dir'] = 'ltr';

        $this->dbiStub = $this->createStub(DatabaseInterface::class);
        $this->dbiStub->method('isMariaDB')->willReturn(true);
        $this->dbiStub->method('escapeString')->willReturnArgument(0);

        $this->requestStub = $this->createStub(ServerRequest::class);
        $this->requestStub->method('isAjax')->willReturn(true);
        $this->requestStub->method('getParsedBodyParam')->willReturn('test.user', 'test.host');

        $this->responseRendererStub = new ResponseRenderer();

        $this->controller = new AccountLockController(
            $this->responseRendererStub,
            new Template(),
            new AccountLocking($this->dbiStub),
        );
    }

    public function testWithValidAccount(): void
    {
        $this->dbiStub->method('getVersion')->willReturn(100402);
        $this->dbiStub->method('tryQuery')->willReturn($this->createStub(DummyResult::class));

        ($this->controller)($this->requestStub);

        $message = Message::success('The account test.user@test.host has been successfully locked.');
        $this->assertEquals(200, $this->responseRendererStub->getResponse()->getStatusCode());
        $this->assertTrue($this->responseRendererStub->hasSuccessState());
        $this->assertEquals(['message' => $message->getDisplay()], $this->responseRendererStub->getJSONResult());
    }

    public function testWithInvalidAccount(): void
    {
        $this->dbiStub->method('getVersion')->willReturn(100402);
        $this->dbiStub->method('tryQuery')->willReturn(false);
        $this->dbiStub->method('getError')->willReturn('Invalid account.');

        ($this->controller)($this->requestStub);

        $message = Message::error('Invalid account.');
        $this->assertEquals(400, $this->responseRendererStub->getResponse()->getStatusCode());
        $this->assertFalse($this->responseRendererStub->hasSuccessState());
        $this->assertEquals(['message' => $message->getDisplay()], $this->responseRendererStub->getJSONResult());
    }

    public function testWithUnsupportedServer(): void
    {
        $this->dbiStub->method('getVersion')->willReturn(100401);

        ($this->controller)($this->requestStub);

        $message = Message::error('Account locking is not supported.');
        $this->assertEquals(400, $this->responseRendererStub->getResponse()->getStatusCode());
        $this->assertFalse($this->responseRendererStub->hasSuccessState());
        $this->assertEquals(['message' => $message->getDisplay()], $this->responseRendererStub->getJSONResult());
    }
}
