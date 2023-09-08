<?php

declare(strict_types=1);

namespace PhpMyAdmin\Tests\Plugins\Schema;

use PhpMyAdmin\Config;
use PhpMyAdmin\DatabaseInterface;
use PhpMyAdmin\Identifiers\DatabaseName;
use PhpMyAdmin\Plugins\Schema\Dia\DiaRelationSchema;
use PhpMyAdmin\Tests\AbstractTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RequiresPhpExtension;

#[CoversClass(DiaRelationSchema::class)]
#[RequiresPhpExtension('xmlwriter')]
class DiaRelationSchemaTest extends AbstractTestCase
{
    protected DiaRelationSchema $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        parent::setUp();

        DatabaseInterface::$instance = $this->createDatabaseInterface();
        $_REQUEST['page_number'] = 33;
        $_REQUEST['dia_show_color'] = true;
        $_REQUEST['dia_show_keys'] = true;
        $_REQUEST['dia_orientation'] = 'orientation';
        $_REQUEST['dia_paper'] = 'paper';
        $_REQUEST['t_v'] = [1 => '1'];
        $_REQUEST['t_h'] = [1 => '1'];
        $_REQUEST['t_x'] = [1 => '10'];
        $_REQUEST['t_y'] = [1 => '10'];
        $_POST['t_db'] = ['test_db'];
        $_POST['t_tbl'] = ['test_table'];

        $GLOBALS['server'] = 1;
        $GLOBALS['db'] = 'test_db';
        Config::getInstance()->selectedServer['DisableIS'] = true;

        $this->object = new DiaRelationSchema(DatabaseName::from('test_db'));
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
     * Test for construct, the Property is set correctly
     */
    #[Group('medium')]
    public function testSetProperty(): void
    {
        $this->assertEquals(33, $this->object->getPageNumber());
        $this->assertTrue($this->object->isShowColor());
        $this->assertTrue($this->object->isShowKeys());
        $this->assertEquals('L', $this->object->getOrientation());
        $this->assertEquals('paper', $this->object->getPaper());
    }
}
