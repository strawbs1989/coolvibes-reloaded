<?php

declare(strict_types=1);

namespace PhpMyAdmin\Tests\Plugins\Export;

use PhpMyAdmin\ConfigStorage\Relation;
use PhpMyAdmin\DatabaseInterface;
use PhpMyAdmin\Export\Export;
use PhpMyAdmin\Plugins\Export\ExportPhparray;
use PhpMyAdmin\Properties\Options\Groups\OptionsPropertyMainGroup;
use PhpMyAdmin\Properties\Options\Groups\OptionsPropertyRootGroup;
use PhpMyAdmin\Properties\Options\Items\HiddenPropertyItem;
use PhpMyAdmin\Properties\Plugins\ExportPluginProperties;
use PhpMyAdmin\Tests\AbstractTestCase;
use PhpMyAdmin\Transformations;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use ReflectionMethod;
use ReflectionProperty;

use function ob_get_clean;
use function ob_start;

#[CoversClass(ExportPhparray::class)]
#[Group('medium')]
class ExportPhparrayTest extends AbstractTestCase
{
    protected ExportPhparray $object;

    /**
     * Configures global environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $dbi = $this->createDatabaseInterface();
        DatabaseInterface::$instance = $dbi;
        $GLOBALS['server'] = 0;
        $GLOBALS['output_kanji_conversion'] = false;
        $GLOBALS['output_charset_conversion'] = false;
        $GLOBALS['buffer_needed'] = false;
        $GLOBALS['asfile'] = true;
        $GLOBALS['save_on_server'] = false;
        $GLOBALS['db'] = '';
        $GLOBALS['table'] = '';
        $GLOBALS['lang'] = 'en';
        $GLOBALS['text_dir'] = 'ltr';
        $this->object = new ExportPhparray(
            new Relation($dbi),
            new Export($dbi),
            new Transformations(),
        );
    }

    /**
     * tearDown for test cases
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->object);
    }

    public function testSetProperties(): void
    {
        $method = new ReflectionMethod(ExportPhparray::class, 'setProperties');
        $method->invoke($this->object, null);

        $attrProperties = new ReflectionProperty(ExportPhparray::class, 'properties');
        $properties = $attrProperties->getValue($this->object);

        $this->assertInstanceOf(ExportPluginProperties::class, $properties);

        $this->assertEquals(
            'PHP array',
            $properties->getText(),
        );

        $this->assertEquals(
            'php',
            $properties->getExtension(),
        );

        $this->assertEquals(
            'text/plain',
            $properties->getMimeType(),
        );

        $this->assertEquals(
            'Options',
            $properties->getOptionsText(),
        );

        $options = $properties->getOptions();

        $this->assertInstanceOf(OptionsPropertyRootGroup::class, $options);

        $this->assertEquals(
            'Format Specific Options',
            $options->getName(),
        );

        $generalOptionsArray = $options->getProperties();
        $generalOptions = $generalOptionsArray->current();

        $this->assertInstanceOf(OptionsPropertyMainGroup::class, $generalOptions);

        $this->assertEquals(
            'general_opts',
            $generalOptions->getName(),
        );

        $generalProperties = $generalOptions->getProperties();

        $property = $generalProperties->current();

        $this->assertInstanceOf(HiddenPropertyItem::class, $property);
    }

    public function testExportHeader(): void
    {
        ob_start();
        $this->assertTrue(
            $this->object->exportHeader(),
        );
        $result = ob_get_clean();

        $this->assertIsString($result);

        $this->assertStringContainsString('<?php' . "\n", $result);
    }

    public function testExportFooter(): void
    {
        $this->assertTrue(
            $this->object->exportFooter(),
        );
    }

    public function testExportDBHeader(): void
    {
        ob_start();
        $this->assertTrue(
            $this->object->exportDBHeader('db'),
        );
        $result = ob_get_clean();

        $this->assertIsString($result);

        $this->assertStringContainsString("/**\n * Database `db`\n */", $result);
    }

    public function testExportDBFooter(): void
    {
        $this->assertTrue(
            $this->object->exportDBFooter('testDB'),
        );
    }

    public function testExportDBCreate(): void
    {
        $this->assertTrue(
            $this->object->exportDBCreate('testDB', 'database'),
        );
    }

    public function testExportData(): void
    {
        ob_start();
        $this->assertTrue(
            $this->object->exportData(
                'test_db',
                'test_table',
                'phpmyadmin.net/err',
                'SELECT * FROM `test_db`.`test_table`;',
            ),
        );
        $result = ob_get_clean();

        $this->assertEquals(
            "\n" . '/* `test_db`.`test_table` */' . "\n" .
            '$test_table = array(' . "\n" .
            '  array(\'id\' => \'1\',\'name\' => \'abcd\',\'datetimefield\' => \'2011-01-20 02:00:02\'),' . "\n" .
            '  array(\'id\' => \'2\',\'name\' => \'foo\',\'datetimefield\' => \'2010-01-20 02:00:02\'),' . "\n" .
            '  array(\'id\' => \'3\',\'name\' => \'Abcd\',\'datetimefield\' => \'2012-01-20 02:00:02\')' . "\n" .
            ');' . "\n",
            $result,
        );

        // case 2: test invalid variable name fix
        ob_start();
        $this->assertTrue(
            $this->object->exportData(
                'test_db',
                '0`932table',
                'phpmyadmin.net/err',
                'SELECT * FROM `test_db`.`test_table`;',
            ),
        );
        $result = ob_get_clean();

        $this->assertIsString($result);
        $this->assertEquals(
            "\n" . '/* `test_db`.`0``932table` */' . "\n" .
            '$_0_932table = array(' . "\n" .
            '  array(\'id\' => \'1\',\'name\' => \'abcd\',\'datetimefield\' => \'2011-01-20 02:00:02\'),' . "\n" .
            '  array(\'id\' => \'2\',\'name\' => \'foo\',\'datetimefield\' => \'2010-01-20 02:00:02\'),' . "\n" .
            '  array(\'id\' => \'3\',\'name\' => \'Abcd\',\'datetimefield\' => \'2012-01-20 02:00:02\')' . "\n" .
            ');' . "\n",
            $result,
        );
    }
}
