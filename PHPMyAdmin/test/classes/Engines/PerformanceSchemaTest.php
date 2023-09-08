<?php

declare(strict_types=1);

namespace PhpMyAdmin\Tests\Engines;

use PhpMyAdmin\DatabaseInterface;
use PhpMyAdmin\Engines\PerformanceSchema;
use PhpMyAdmin\Tests\AbstractTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(PerformanceSchema::class)]
class PerformanceSchemaTest extends AbstractTestCase
{
    protected PerformanceSchema $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        parent::setUp();

        DatabaseInterface::$instance = $this->createDatabaseInterface();
        $GLOBALS['server'] = 0;
        $this->object = new PerformanceSchema('PERFORMANCE_SCHEMA');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->object);
    }

    /**
     * Test for getMysqlHelpPage
     */
    public function testGetMysqlHelpPage(): void
    {
        $this->assertEquals(
            $this->object->getMysqlHelpPage(),
            'performance-schema',
        );
    }
}
