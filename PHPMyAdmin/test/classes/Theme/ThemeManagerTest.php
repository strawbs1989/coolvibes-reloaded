<?php

declare(strict_types=1);

namespace PhpMyAdmin\Tests\Theme;

use PhpMyAdmin\Config;
use PhpMyAdmin\DatabaseInterface;
use PhpMyAdmin\Tests\AbstractTestCase;
use PhpMyAdmin\Theme\ThemeManager;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ThemeManager::class)]
class ThemeManagerTest extends AbstractTestCase
{
    /**
     * SetUp for test cases
     */
    protected function setUp(): void
    {
        parent::setUp();

        parent::setGlobalConfig();

        $config = Config::getInstance();
        $config->settings['ThemePerServer'] = false;
        $config->settings['ThemeDefault'] = 'pmahomme';
        $config->settings['ServerDefault'] = 0;
        $GLOBALS['server'] = 99;

        $dbi = $this->getMockBuilder(DatabaseInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $dbi->expects($this->any())->method('escapeString')
            ->willReturnArgument(0);
    }

    /**
     * Test for ThemeManager::getThemeCookieName
     */
    public function testCookieName(): void
    {
        $tm = new ThemeManager();
        $this->assertEquals('pma_theme', $tm->getThemeCookieName());
    }

    /**
     * Test for ThemeManager::getThemeCookieName
     */
    public function testPerServerCookieName(): void
    {
        $tm = new ThemeManager();
        $tm->setThemePerServer(true);
        $this->assertEquals('pma_theme-99', $tm->getThemeCookieName());
    }

    public function testGetThemesArray(): void
    {
        $tm = new ThemeManager();
        $tm->initializeTheme();
        $themes = $tm->getThemesArray();
        $this->assertIsArray($themes);
        $this->assertArrayHasKey(0, $themes);
        $this->assertIsArray($themes[0]);
        $this->assertArrayHasKey('id', $themes[0]);
        $this->assertArrayHasKey('name', $themes[0]);
        $this->assertArrayHasKey('version', $themes[0]);
        $this->assertArrayHasKey('is_active', $themes[0]);
    }

    /**
     * Test for setThemeCookie
     */
    public function testSetThemeCookie(): void
    {
        $tm = new ThemeManager();
        $this->assertTrue(
            $tm->setThemeCookie(),
        );
    }
}
