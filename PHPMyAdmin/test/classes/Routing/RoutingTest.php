<?php

declare(strict_types=1);

namespace PhpMyAdmin\Tests\Routing;

use FastRoute\Dispatcher;
use PhpMyAdmin\Config;
use PhpMyAdmin\Controllers\HomeController;
use PhpMyAdmin\Routing\Routing;
use PhpMyAdmin\Tests\AbstractTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

use function copy;
use function unlink;

use const CACHE_DIR;
use const TEST_PATH;

#[CoversClass(Routing::class)]
class RoutingTest extends AbstractTestCase
{
    /**
     * Test for Routing::getDispatcher
     */
    public function testGetDispatcher(): void
    {
        $expected = [Dispatcher::FOUND, HomeController::class, []];
        $cacheFilename = CACHE_DIR . 'routes.cache.php';
        $validCacheFilename = TEST_PATH . 'test/test_data/routes/routes-valid.cache.txt';
        $invalidCacheFilename = TEST_PATH . 'test/test_data/routes/routes-invalid.cache.txt';
        $config = Config::getInstance();
        $config->settings['environment'] = null;

        $this->assertDirectoryIsWritable(CACHE_DIR);

        // Valid cache file.
        $this->assertTrue(copy($validCacheFilename, $cacheFilename));
        $dispatcher = Routing::getDispatcher();
        $this->assertSame($expected, $dispatcher->dispatch('GET', '/'));
        $this->assertFileEquals($validCacheFilename, $cacheFilename);

        // Invalid cache file.
        $this->assertTrue(copy($invalidCacheFilename, $cacheFilename));
        $dispatcher = Routing::getDispatcher();
        $this->assertSame($expected, $dispatcher->dispatch('GET', '/'));
        $this->assertFileNotEquals($invalidCacheFilename, $cacheFilename);

        // Create new cache file.
        $this->assertTrue(unlink($cacheFilename));

        $this->assertFileDoesNotExist($cacheFilename);

        $dispatcher = Routing::getDispatcher();
        $this->assertSame($expected, $dispatcher->dispatch('GET', '/'));
        $this->assertFileExists($cacheFilename);

        // Without a cache file.
        $config->settings['environment'] = 'development';
        $dispatcher = Routing::getDispatcher();
        $this->assertSame($expected, $dispatcher->dispatch('GET', '/'));
    }

    /**
     * @param string $phpSelf  The PHP_SELF value
     * @param string $request  The REQUEST_URI value
     * @param string $pathInfo The PATH_INFO value
     * @param string $expected Expected result
     */
    #[DataProvider('providerForTestCleanupPathInfo')]
    public function testCleanupPathInfo(string $phpSelf, string $request, string $pathInfo, string $expected): void
    {
        $_SERVER['PHP_SELF'] = $phpSelf;
        $_SERVER['REQUEST_URI'] = $request;
        $_SERVER['PATH_INFO'] = $pathInfo;
        $actual = Routing::getCleanPathInfo();
        $this->assertEquals($expected, $actual);
    }

    /** @return array<array{string, string, string, string}> */
    public static function providerForTestCleanupPathInfo(): array
    {
        return [
            [
                '/phpmyadmin/index.php/; cookieinj=value/',
                '/phpmyadmin/index.php/;%20cookieinj=value///',
                '/; cookieinj=value/',
                '/phpmyadmin/index.php',
            ],
            ['', '/phpmyadmin/index.php/;%20cookieinj=value///', '/; cookieinj=value/', '/phpmyadmin/index.php'],
            ['', '//example.com/../phpmyadmin/index.php', '', '/phpmyadmin/index.php'],
            ['', '//example.com/../../.././phpmyadmin/index.php', '', '/phpmyadmin/index.php'],
            ['', '/page.php/malicouspathinfo?malicouspathinfo', 'malicouspathinfo', '/page.php'],
            ['/phpmyadmin/./index.php', '/phpmyadmin/./index.php', '', '/phpmyadmin/index.php'],
            ['/phpmyadmin/index.php', '/phpmyadmin/index.php', '', '/phpmyadmin/index.php'],
            ['', '/phpmyadmin/index.php', '', '/phpmyadmin/index.php'],
        ];
    }
}
