<?php

declare(strict_types=1);

namespace PhpMyAdmin\Tests\Navigation\Nodes;

use PhpMyAdmin\Config;
use PhpMyAdmin\ConfigStorage\Relation;
use PhpMyAdmin\ConfigStorage\RelationParameters;
use PhpMyAdmin\Navigation\Nodes\NodeDatabase;
use PhpMyAdmin\Navigation\Nodes\NodeDatabaseChild;
use PhpMyAdmin\Tests\AbstractTestCase;
use PhpMyAdmin\Url;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionProperty;

#[CoversClass(NodeDatabaseChild::class)]
#[CoversClass(NodeDatabase::class)]
class NodeDatabaseChildTest extends AbstractTestCase
{
    /**
     * Mock of NodeDatabaseChild
     *
     * @var NodeDatabaseChild&MockObject
     */
    protected NodeDatabaseChild $object;

    /**
     * Sets up the fixture.
     */
    protected function setUp(): void
    {
        parent::setUp();

        parent::setTheme();

        parent::setLanguage();

        $config = Config::getInstance();
        $config->settings['DefaultTabDatabase'] = 'structure';
        $GLOBALS['server'] = 1;
        $config->settings['ServerDefault'] = 1;
        $relationParameters = RelationParameters::fromArray([
            'db' => 'pmadb',
            'navwork' => true,
            'navigationhiding' => 'navigationhiding',
        ]);
        (new ReflectionProperty(Relation::class, 'cache'))->setValue(null, $relationParameters);
        $this->object = $this->getMockForAbstractClass(
            NodeDatabaseChild::class,
            ['child'],
        );
    }

    /**
     * Tears down the fixture.
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->object);
    }

    /**
     * Tests getHtmlForControlButtons() method
     */
    public function testGetHtmlForControlButtons(): void
    {
        $relationParameters = RelationParameters::fromArray([
            'db' => 'pmadb',
            'navwork' => true,
            'navigationhiding' => 'navigationhiding',
        ]);

        $parent = new NodeDatabase('parent');
        $parent->addChild($this->object);
        $this->object->expects($this->once())
            ->method('getItemType')
            ->willReturn('itemType');
        $html = $this->object->getHtmlForControlButtons($relationParameters->navigationItemsHidingFeature);

        $this->assertStringStartsWith('<span class="navItemControls">', $html);
        $this->assertStringEndsWith('</span>', $html);
        $this->assertStringContainsString(
            '<a href="' . Url::getFromRoute('/navigation') . '" data-post="'
            . 'hideNavItem=1&itemType=itemType&itemName=child'
            . '&dbName=parent&lang=en" class="hideNavItem ajax">',
            $html,
        );
    }
}
