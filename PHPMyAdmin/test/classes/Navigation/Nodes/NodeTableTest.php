<?php

declare(strict_types=1);

namespace PhpMyAdmin\Tests\Navigation\Nodes;

use PhpMyAdmin\Config;
use PhpMyAdmin\Navigation\Nodes\NodeTable;
use PhpMyAdmin\Tests\AbstractTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(NodeTable::class)]
class NodeTableTest extends AbstractTestCase
{
    /**
     * Test for __construct
     */
    public function testConstructor(): void
    {
        $config = Config::getInstance();
        $config->settings['NavigationTreeDefaultTabTable'] = 'search';
        $config->settings['NavigationTreeDefaultTabTable2'] = 'insert';

        $parent = new NodeTable('default');
        $this->assertEquals(
            [
                'text' => ['route' => '/sql', 'params' => ['pos' => 0, 'db' => null, 'table' => null]],
                'icon' => ['route' => '/table/search', 'params' => ['db' => null, 'table' => null]],
                'second_icon' => ['route' => '/table/change', 'params' => ['db' => null, 'table' => null]],
                'title' => 'Browse',
            ],
            $parent->links,
        );
        $this->assertStringContainsString('table', $parent->classes);
    }

    /**
     * Tests whether the node icon is properly set based on the icon target.
     *
     * @param string $target    target of the icon
     * @param string $imageName name of the image that should be set
     */
    #[DataProvider('providerForTestIcon')]
    public function testIcon(string $target, string $imageName, string $imageTitle): void
    {
        Config::getInstance()->settings['NavigationTreeDefaultTabTable'] = $target;
        $node = new NodeTable('default');
        $this->assertEquals($imageName, $node->icon['image']);
        $this->assertEquals($imageTitle, $node->icon['title']);
    }

    /**
     * Data provider for testIcon().
     *
     * @return mixed[] data for testIcon()
     */
    public static function providerForTestIcon(): array
    {
        return [
            ['structure', 'b_props', 'Structure'],
            ['search', 'b_search', 'Search'],
            ['insert', 'b_insrow', 'Insert'],
            ['sql', 'b_sql', 'SQL'],
            ['browse', 'b_browse', 'Browse'],
        ];
    }
}
