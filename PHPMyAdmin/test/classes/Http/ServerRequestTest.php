<?php

declare(strict_types=1);

namespace PhpMyAdmin\Tests\Http;

use PhpMyAdmin\Http\Factory\ServerRequestFactory;
use PhpMyAdmin\Http\ServerRequest;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

#[CoversClass(ServerRequest::class)]
class ServerRequestTest extends TestCase
{
    /**
     * @param array<string, string> $get
     * @param array<string, string> $post
     */
    #[DataProvider('providerForTestGetRoute')]
    public function testGetRoute(string $expected, array $get, array $post): void
    {
        $requestStub = $this->createStub(ServerRequestInterface::class);
        $requestStub->method('getQueryParams')->willReturn($get);
        $requestStub->method('getParsedBody')->willReturn($post);
        $request = new ServerRequest($requestStub);
        $this->assertSame($expected, $request->getRoute());
    }

    /**
     * @return array<int, array<int, array<string, string>|string>>
     * @psalm-return array<int, array{string, array<string, string>, array<string, string>}>
     */
    public static function providerForTestGetRoute(): iterable
    {
        return [
            ['/', [], []],
            ['/test', ['route' => '/test'], []],
            ['/test', [], ['route' => '/test']],
            ['/test-get', ['route' => '/test-get'], ['route' => '/test-post']],
            ['/', ['db' => 'db'], []],
            ['/', ['db' => 'db', 'table' => 'table'], []],
            ['/test', ['route' => '/test', 'db' => 'db'], []],
            ['/test', ['route' => '/test', 'db' => 'db', 'table' => 'table'], []],
            ['/', [], ['db' => 'db']],
            ['/', [], ['db' => 'db', 'table' => 'table']],
        ];
    }

    public function testGetQueryParam(): void
    {
        $queryParams = ['key1' => 'value1', 'key2' => ['value2'], 'key4' => ''];
        $requestStub = $this->createStub(ServerRequestInterface::class);
        $requestStub->method('getQueryParams')->willReturn($queryParams);
        $request = new ServerRequest($requestStub);
        $this->assertSame('value1', $request->getQueryParam('key1'));
        $this->assertSame('value1', $request->getQueryParam('key1', 'default'));
        $this->assertSame(['value2'], $request->getQueryParam('key2'));
        $this->assertSame(['value2'], $request->getQueryParam('key2', 'default'));
        $this->assertNull($request->getQueryParam('key3'));
        $this->assertSame('default', $request->getQueryParam('key3', 'default'));
        $this->assertSame('', $request->getQueryParam('key4'));
        $this->assertSame('', $request->getQueryParam('key4', 'default'));
    }

    public function testHasBodyParam(): void
    {
        $queryParams = ['key1' => 'value1', 'key2' => ['value2'], 'key4' => ''];
        $requestStub = $this->createStub(ServerRequestInterface::class);
        $requestStub->method('getParsedBody')->willReturn($queryParams);
        $request = new ServerRequest($requestStub);
        $this->assertTrue($request->hasBodyParam('key1'));
        $this->assertTrue($request->hasBodyParam('key2'));
        $this->assertFalse($request->hasBodyParam('key3'));
        $this->assertTrue($request->hasBodyParam('key4'));
    }

    public function testHasQueryParam(): void
    {
        $queryParams = ['key1' => 'value1', 'key2' => ['value2'], 'key4' => ''];
        $requestStub = $this->createStub(ServerRequestInterface::class);
        $requestStub->method('getQueryParams')->willReturn($queryParams);
        $request = new ServerRequest($requestStub);
        $this->assertTrue($request->hasQueryParam('key1'));
        $this->assertTrue($request->has('key1'));
        $this->assertTrue($request->hasQueryParam('key2'));
        $this->assertTrue($request->has('key2'));
        $this->assertFalse($request->hasQueryParam('key3'));
        $this->assertFalse($request->has('key3'));
        $this->assertTrue($request->hasQueryParam('key4'));
        $this->assertTrue($request->has('key4'));
    }

    /**
     * @psalm-param array<string, string> $headers
     * @psalm-param array<string, string>|null $body
     */
    #[DataProvider('isAjaxProvider')]
    public function testIsAjax(bool $expected, string $method, string $uri, array $headers, array|null $body): void
    {
        $request = ServerRequestFactory::create()->createServerRequest($method, $uri)->withParsedBody($body);
        foreach ($headers as $name => $value) {
            $request = $request->withAddedHeader($name, $value);
        }

        $this->assertSame($expected, $request->isAjax());
    }

    /** @return iterable<int, array{bool, string, string, array<string, string>, array<string, string>|null}> */
    public static function isAjaxProvider(): iterable
    {
        return [
            [true, 'GET', 'http://example.com/index.php?route=/&ajax_request=1', [], null],
            [true, 'GET', 'http://example.com/index.php?route=/&ajax_request=0', [], null],
            [true, 'GET', 'http://example.com/index.php?route=/&ajax_request=true', [], null],
            [true, 'GET', 'http://example.com/index.php?route=/', ['X-Requested-With' => 'XMLHttpRequest'], null],
            [
                true,
                'GET',
                'http://example.com/index.php?route=/&ajax_request=1',
                ['X-Requested-With' => 'XMLHttpRequest'],
                null,
            ],
            [false, 'GET', 'http://example.com/index.php?route=/', [], null],
            [true, 'POST', 'http://example.com/index.php?route=/&ajax_request=1', [], []],
            [true, 'POST', 'http://example.com/index.php?route=/', ['X-Requested-With' => 'XMLHttpRequest'], []],
            [
                true,
                'POST',
                'http://example.com/index.php?route=/&ajax_request=1',
                ['X-Requested-With' => 'XMLHttpRequest'],
                [],
            ],
            [true, 'POST', 'http://example.com/index.php?route=/&ajax_request=1', [], ['ajax_request' => '1']],
            [
                true,
                'POST',
                'http://example.com/index.php?route=/&ajax_request=1',
                ['X-Requested-With' => 'XMLHttpRequest'],
                ['ajax_request' => '1'],
            ],
            [
                true,
                'POST',
                'http://example.com/index.php?route=/',
                ['X-Requested-With' => 'XMLHttpRequest'],
                ['ajax_request' => '1'],
            ],
            [true, 'POST', 'http://example.com/index.php?route=/', [], ['ajax_request' => '1']],
            [false, 'POST', 'http://example.com/index.php?route=/', [], []],
        ];
    }
}
