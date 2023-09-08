<?php

declare(strict_types=1);

namespace PhpMyAdmin\Tests\Server\SysInfo;

use PhpMyAdmin\Server\SysInfo\Base;
use PhpMyAdmin\Server\SysInfo\SysInfo;
use PhpMyAdmin\Tests\AbstractTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(SysInfo::class)]
class SysInfoTest extends AbstractTestCase
{
    /**
     * Test for OS detection
     *
     * @param string $os       OS name as returned by PHP_OS
     * @param string $expected Expected detected OS name
     */
    #[DataProvider('sysInfoOsProvider')]
    public function testGetSysInfoOs(string $os, string $expected): void
    {
        $this->assertEquals(
            $expected,
            SysInfo::getOs($os),
        );
    }

    /**
     * Data provider for OS detection tests.
     *
     * @return string[][]
     */
    public static function sysInfoOsProvider(): array
    {
        return [['FreeBSD', 'Linux'], ['Linux', 'Linux'], ['Winnt', 'Winnt'], ['SunOS', 'SunOS']];
    }

    /**
     * Test for getting sysinfo object.
     */
    public function testGetSysInfo(): void
    {
        $this->assertInstanceOf(Base::class, SysInfo::get());
    }

    /**
     * Test for getting supported sysinfo object.
     */
    public function testGetSysInfoSupported(): void
    {
        $this->assertTrue(SysInfo::get()::isSupported());
    }
}
