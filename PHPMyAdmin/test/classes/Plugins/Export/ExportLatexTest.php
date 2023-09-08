<?php

declare(strict_types=1);

namespace PhpMyAdmin\Tests\Plugins\Export;

use PhpMyAdmin\Column;
use PhpMyAdmin\Config;
use PhpMyAdmin\ConfigStorage\Relation;
use PhpMyAdmin\ConfigStorage\RelationParameters;
use PhpMyAdmin\DatabaseInterface;
use PhpMyAdmin\Export\Export;
use PhpMyAdmin\Plugins\Export\ExportLatex;
use PhpMyAdmin\Properties\Options\Groups\OptionsPropertyMainGroup;
use PhpMyAdmin\Properties\Options\Groups\OptionsPropertyRootGroup;
use PhpMyAdmin\Properties\Options\Items\BoolPropertyItem;
use PhpMyAdmin\Properties\Options\Items\RadioPropertyItem;
use PhpMyAdmin\Properties\Options\Items\TextPropertyItem;
use PhpMyAdmin\Properties\Plugins\ExportPluginProperties;
use PhpMyAdmin\Tests\AbstractTestCase;
use PhpMyAdmin\Tests\Stubs\DummyResult;
use PhpMyAdmin\Transformations;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use ReflectionMethod;
use ReflectionProperty;

use function __;
use function ob_get_clean;
use function ob_start;

#[CoversClass(ExportLatex::class)]
#[Group('medium')]
class ExportLatexTest extends AbstractTestCase
{
    protected ExportLatex $object;

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
        $GLOBALS['plugin_param'] = [];
        $GLOBALS['plugin_param']['export_type'] = 'table';
        $GLOBALS['plugin_param']['single_table'] = false;
        $GLOBALS['db'] = 'db';
        $GLOBALS['table'] = 'table';
        $this->object = new ExportLatex(
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

        DatabaseInterface::$instance = null;
        unset($this->object);
    }

    public function testSetProperties(): void
    {
        $GLOBALS['plugin_param']['export_type'] = '';
        $GLOBALS['plugin_param']['single_table'] = false;

        $relationParameters = RelationParameters::fromArray([
            'db' => 'db',
            'relation' => 'relation',
            'column_info' => 'column_info',
            'relwork' => true,
            'mimework' => true,
        ]);
        (new ReflectionProperty(Relation::class, 'cache'))->setValue(null, $relationParameters);

        $method = new ReflectionMethod(ExportLatex::class, 'setProperties');
        $properties = $method->invoke($this->object, null);

        $this->assertInstanceOf(ExportPluginProperties::class, $properties);

        $this->assertEquals(
            'LaTeX',
            $properties->getText(),
        );

        $this->assertEquals(
            'tex',
            $properties->getExtension(),
        );

        $this->assertEquals(
            'application/x-tex',
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
        $generalOptionsArray->next();

        $this->assertInstanceOf(OptionsPropertyMainGroup::class, $generalOptions);

        $this->assertEquals(
            'general_opts',
            $generalOptions->getName(),
        );

        $generalProperties = $generalOptions->getProperties();

        $property = $generalProperties->current();

        $this->assertInstanceOf(BoolPropertyItem::class, $property);

        $this->assertEquals(
            'caption',
            $property->getName(),
        );

        $this->assertEquals(
            'Include table caption',
            $property->getText(),
        );

        $generalOptions = $generalOptionsArray->current();
        $generalOptionsArray->next();

        $this->assertInstanceOf(OptionsPropertyMainGroup::class, $generalOptions);

        $this->assertEquals(
            'dump_what',
            $generalOptions->getName(),
        );

        $this->assertEquals(
            'Dump table',
            $generalOptions->getText(),
        );

        $generalProperties = $generalOptions->getProperties();

        $property = $generalProperties->current();

        $this->assertInstanceOf(RadioPropertyItem::class, $property);

        $this->assertEquals(
            'structure_or_data',
            $property->getName(),
        );

        $this->assertEquals(
            ['structure' => __('structure'), 'data' => __('data'), 'structure_and_data' => __('structure and data')],
            $property->getValues(),
        );

        // hide structure
        $generalOptions = $generalOptionsArray->current();
        $generalOptionsArray->next();

        $this->assertInstanceOf(OptionsPropertyMainGroup::class, $generalOptions);

        $this->assertEquals(
            'structure',
            $generalOptions->getName(),
        );

        $this->assertEquals(
            'Object creation options',
            $generalOptions->getText(),
        );

        $this->assertEquals(
            'data',
            $generalOptions->getForce(),
        );

        $generalProperties = $generalOptions->getProperties();

        $property = $generalProperties->current();
        $generalProperties->next();

        $this->assertInstanceOf(TextPropertyItem::class, $property);

        $this->assertEquals(
            'structure_caption',
            $property->getName(),
        );

        $this->assertEquals(
            'Table caption:',
            $property->getText(),
        );

        $this->assertEquals(
            'faq6-27',
            $property->getDoc(),
        );

        $property = $generalProperties->current();
        $generalProperties->next();

        $this->assertInstanceOf(TextPropertyItem::class, $property);

        $this->assertEquals(
            'structure_continued_caption',
            $property->getName(),
        );

        $this->assertEquals(
            'Table caption (continued):',
            $property->getText(),
        );

        $this->assertEquals(
            'faq6-27',
            $property->getDoc(),
        );

        $property = $generalProperties->current();
        $generalProperties->next();

        $this->assertInstanceOf(TextPropertyItem::class, $property);

        $this->assertEquals(
            'structure_label',
            $property->getName(),
        );

        $this->assertEquals(
            'Label key:',
            $property->getText(),
        );

        $this->assertEquals(
            'faq6-27',
            $property->getDoc(),
        );

        $property = $generalProperties->current();
        $generalProperties->next();

        $this->assertInstanceOf(BoolPropertyItem::class, $property);

        $this->assertEquals(
            'relation',
            $property->getName(),
        );

        $this->assertEquals(
            'Display foreign key relationships',
            $property->getText(),
        );

        $property = $generalProperties->current();
        $generalProperties->next();

        $this->assertInstanceOf(BoolPropertyItem::class, $property);

        $this->assertEquals(
            'comments',
            $property->getName(),
        );

        $this->assertEquals(
            'Display comments',
            $property->getText(),
        );

        $property = $generalProperties->current();

        $this->assertInstanceOf(BoolPropertyItem::class, $property);

        $this->assertEquals(
            'mime',
            $property->getName(),
        );

        $this->assertEquals(
            'Display media types',
            $property->getText(),
        );

        // data options
        $generalOptions = $generalOptionsArray->current();

        $this->assertInstanceOf(OptionsPropertyMainGroup::class, $generalOptions);

        $this->assertEquals(
            'data',
            $generalOptions->getName(),
        );

        $this->assertEquals(
            'Data dump options',
            $generalOptions->getText(),
        );

        $this->assertEquals(
            'structure',
            $generalOptions->getForce(),
        );

        $generalProperties = $generalOptions->getProperties();

        $property = $generalProperties->current();
        $generalProperties->next();

        $this->assertInstanceOf(BoolPropertyItem::class, $property);

        $this->assertEquals(
            'columns',
            $property->getName(),
        );

        $this->assertEquals(
            'Put columns names in the first row:',
            $property->getText(),
        );

        $property = $generalProperties->current();
        $generalProperties->next();

        $this->assertInstanceOf(TextPropertyItem::class, $property);

        $this->assertEquals(
            'data_caption',
            $property->getName(),
        );

        $this->assertEquals(
            'Table caption:',
            $property->getText(),
        );

        $this->assertEquals(
            'faq6-27',
            $property->getDoc(),
        );

        $property = $generalProperties->current();
        $generalProperties->next();

        $this->assertInstanceOf(TextPropertyItem::class, $property);

        $this->assertEquals(
            'data_continued_caption',
            $property->getName(),
        );

        $this->assertEquals(
            'Table caption (continued):',
            $property->getText(),
        );

        $this->assertEquals(
            'faq6-27',
            $property->getDoc(),
        );

        $property = $generalProperties->current();
        $generalProperties->next();

        $this->assertInstanceOf(TextPropertyItem::class, $property);

        $this->assertEquals(
            'data_label',
            $property->getName(),
        );

        $this->assertEquals(
            'Label key:',
            $property->getText(),
        );

        $this->assertEquals(
            'faq6-27',
            $property->getDoc(),
        );

        $property = $generalProperties->current();

        $this->assertInstanceOf(TextPropertyItem::class, $property);

        $this->assertEquals(
            'null',
            $property->getName(),
        );

        $this->assertEquals(
            'Replace NULL with:',
            $property->getText(),
        );

        // case 2
        $GLOBALS['plugin_param']['export_type'] = 'table';
        $GLOBALS['plugin_param']['single_table'] = false;

        $method->invoke($this->object, null);

        $generalOptionsArray = $options->getProperties();

        $this->assertCount(4, $generalOptionsArray);
    }

    public function testExportHeader(): void
    {
        $config = Config::getInstance();
        $config->selectedServer['port'] = 80;
        $config->selectedServer['host'] = 'localhost';

        ob_start();
        $this->assertTrue(
            $this->object->exportHeader(),
        );
        $result = ob_get_clean();

        $this->assertIsString($result);

        $this->assertStringContainsString("\n% Host: localhost:80", $result);
    }

    public function testExportFooter(): void
    {
        $this->assertTrue(
            $this->object->exportFooter(),
        );
    }

    public function testExportDBHeader(): void
    {
        $this->expectOutputString("% \n% Database: 'testDB'\n% \n");

        $this->assertTrue(
            $this->object->exportDBHeader('testDB'),
        );
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
        $GLOBALS['latex_caption'] = true;
        $GLOBALS['latex_data_caption'] = 'latex data caption';
        $GLOBALS['latex_data_continued_caption'] = 'continued caption';
        $GLOBALS['latex_columns'] = true;
        $GLOBALS['latex_data_label'] = 'datalabel';
        $GLOBALS['latex_null'] = 'null';
        $config = Config::getInstance();
        $config->selectedServer['host'] = 'localhost';
        $config->selectedServer['verbose'] = 'verb';

        ob_start();
        $this->assertTrue($this->object->exportData(
            'test_db',
            'test_table',
            'localhost',
            'SELECT * FROM `test_db`.`test_table`;',
        ));
        $result = ob_get_clean();

        $this->assertEquals(
            "\n" . '%' . "\n" .
            '% Data: test_table' . "\n" .
            '%' . "\n" .
            ' \begin{longtable}{|l|l|l|} ' . "\n" .
            ' \hline \endhead \hline \endfoot \hline ' . "\n" .
            ' \caption{latex data caption} \label{datalabel} \\\\\hline \multicolumn{1}{|c|}' .
            '{\textbf{id}} & \multicolumn{1}{|c|}{\textbf{name}} & \multicolumn{1}{|c|}' .
            '{\textbf{datetimefield}} \\\ \hline \hline  \endfirsthead ' . "\n" .
            '\caption{continued caption} \\\ \hline \multicolumn{1}{|c|}{\textbf{id}} & \multicolumn{1}' .
            '{|c|}{\textbf{name}} & \multicolumn{1}{|c|}{\textbf{datetimefield}}' .
            ' \\\ \hline \hline \endhead \endfoot' . "\n" .
            '1 & abcd & 2011-01-20 02:00:02 \\\\ \hline ' . "\n" .
            '2 & foo & 2010-01-20 02:00:02 \\\\ \hline ' . "\n" .
            '3 & Abcd & 2012-01-20 02:00:02 \\\\ \hline ' . "\n" .
            ' \end{longtable}' . "\n",
            $result,
        );

        // case 2
        unset($GLOBALS['latex_columns']);

        ob_start();
        $this->assertTrue($this->object->exportData(
            'test_db',
            'test_table',
            'localhost',
            'SELECT * FROM `test_db`.`test_table`;',
        ));
        $result = ob_get_clean();

        $this->assertIsString($result);
        $this->assertEquals(
            "\n" . '%' . "\n" .
            '% Data: test_table' . "\n" .
            '%' . "\n" .
            ' \begin{longtable}{|l|l|l|} ' . "\n" .
            ' \hline \endhead \hline \endfoot \hline ' . "\n" .
            ' \caption{latex data caption} \label{datalabel} \\\\\\\\ \hline' .
            '1 & abcd & 2011-01-20 02:00:02 \\\\ \hline ' . "\n" .
            '2 & foo & 2010-01-20 02:00:02 \\\\ \hline ' . "\n" .
            '3 & Abcd & 2012-01-20 02:00:02 \\\\ \hline ' . "\n" .
            ' \end{longtable}' . "\n",
            $result,
        );
    }

    public function testExportStructure(): void
    {
        $keys = [['Non_unique' => 0, 'Column_name' => 'name1'], ['Non_unique' => 1, 'Column_name' => 'name2']];

        // case 1

        $resultStub = $this->createMock(DummyResult::class);

        $dbi = $this->getMockBuilder(DatabaseInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $dbi->expects($this->once())
            ->method('getTableIndexes')
            ->with('database', '')
            ->willReturn($keys);

        $dbi->expects($this->exactly(2))
            ->method('fetchResult')
            ->willReturn(
                [],
                ['name1' => ['values' => 'test-', 'transformation' => 'testfoo', 'mimetype' => 'testmimetype_']],
            );

        $columns = [
            new Column('name1', 'set(abc)enum123', true, 'PRI', null, ''),
            new Column('fields', '', false, 'COMP', 'def', ''),
        ];
        $dbi->expects($this->once())
            ->method('getColumns')
            ->with('database', '')
            ->willReturn($columns);

        $dbi->expects($this->once())
            ->method('tryQueryAsControlUser')
            ->willReturn($resultStub);

        $resultStub->expects($this->once())
            ->method('numRows')
            ->willReturn(1);

        $resultStub->expects($this->once())
            ->method('fetchAssoc')
            ->willReturn(['comment' => 'testComment']);

        DatabaseInterface::$instance = $dbi;
        $this->object->relation = new Relation($dbi);
        if (isset($GLOBALS['latex_caption'])) {
            unset($GLOBALS['latex_caption']);
        }

        $relationParameters = RelationParameters::fromArray([
            'relwork' => true,
            'commwork' => true,
            'mimework' => true,
            'db' => 'database',
            'relation' => 'rel',
            'column_info' => 'col',
        ]);
        (new ReflectionProperty(Relation::class, 'cache'))->setValue(null, $relationParameters);

        ob_start();
        $this->assertTrue(
            $this->object->exportStructure(
                'database',
                '',
                'example.com',
                'test',
                'test',
                true,
                true,
                true,
            ),
        );
        $result = ob_get_clean();

        //echo $result; die;
        $this->assertEquals(
            "\n" . '%' . "\n" .
            '% Structure: ' . "\n" .
            '%' . "\n" .
            ' \\begin{longtable}{|l|c|c|c|l|l|} ' . "\n" .
            ' \\hline \\multicolumn{1}{|c|}{\\textbf{Column}} & ' .
            '\\multicolumn{1}{|c|}{\\textbf{Type}} & \\multicolumn{1}{|c|}' .
            '{\\textbf{Null}} & \\multicolumn{1}{|c|}{\\textbf{Default}} &' .
            ' \\multicolumn{1}{|c|}{\\textbf{Comments}} & \\multicolumn{1}' .
            '{|c|}{\\textbf{MIME}} \\\\ \\hline \\hline' . "\n" .
            '\\endfirsthead' . "\n" . ' \\hline \\multicolumn{1}{|c|}' .
            '{\\textbf{Column}} & \\multicolumn{1}{|c|}{\\textbf{Type}}' .
            ' & \\multicolumn{1}{|c|}{\\textbf{Null}} & \\multicolumn' .
            '{1}{|c|}{\\textbf{Default}} & \\multicolumn{1}{|c|}{\\textbf' .
            '{Comments}} & \\multicolumn{1}{|c|}{\\textbf{MIME}} \\\\ ' .
            '\\hline \\hline \\endhead \\endfoot ' . "\n" . '\\textbf{\\textit' .
            '{name1}} & set(abc) & Yes & NULL &  ' .
            '& Testmimetype/ \\\\ \\hline ' . "\n" .
            'fields &   & No & def &  &  \\\\ \\hline ' . "\n" .
            ' \\end{longtable}' . "\n",
            $result,
        );

        // case 2

        $resultStub = $this->createMock(DummyResult::class);

        $dbi = $this->getMockBuilder(DatabaseInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $dbi->expects($this->exactly(2))
            ->method('fetchResult')
            ->willReturn(
                ['name1' => ['foreign_table' => 'ftable', 'foreign_field' => 'ffield'], 'foreign_keys_data' => []],
                ['field' => ['values' => 'test-', 'transformation' => 'testfoo', 'mimetype' => 'test<']],
            );

        $dbi->expects($this->once())
            ->method('getTableIndexes')
            ->with('database', '')
            ->willReturn($keys);

        $dbi->expects($this->once())
            ->method('getColumns')
            ->with('database', '')
            ->willReturn($columns);

        $dbi->expects($this->once())
            ->method('tryQueryAsControlUser')
            ->willReturn($resultStub);

        $resultStub->expects($this->once())
            ->method('numRows')
            ->willReturn(1);

        $resultStub->expects($this->once())
            ->method('fetchAssoc')
            ->willReturn(['comment' => 'testComment']);

        DatabaseInterface::$instance = $dbi;
        $this->object->relation = new Relation($dbi);

        $relationParameters = RelationParameters::fromArray([
            'relwork' => true,
            'commwork' => true,
            'mimework' => true,
            'db' => 'database',
            'relation' => 'rel',
            'column_info' => 'col',
        ]);
        (new ReflectionProperty(Relation::class, 'cache'))->setValue(null, $relationParameters);

        ob_start();
        $this->assertTrue(
            $this->object->exportStructure(
                'database',
                '',
                'example.com',
                'test',
                'test',
                true,
                true,
                true,
            ),
        );
        $result = ob_get_clean();

        $this->assertIsString($result);

        $this->assertStringContainsString(
            '\\textbf{\\textit{name1}} & set(abc) & Yes & NULL & ' .
            'ftable (ffield) &  &  \\\\ \\hline',
            $result,
        );

        // case 3

        $dbi = $this->getMockBuilder(DatabaseInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $dbi->expects($this->once())
            ->method('getTableIndexes')
            ->with('database', '')
            ->willReturn($keys);

        $dbi->expects($this->once())
            ->method('getColumns')
            ->with('database', '')
            ->willReturn($columns);

        $dbi->expects($this->never())
            ->method('tryQuery');

        DatabaseInterface::$instance = $dbi;

        $GLOBALS['latex_caption'] = true;
        $GLOBALS['latex_structure_caption'] = 'latexstructure';
        $GLOBALS['latex_structure_label'] = 'latexlabel';
        $GLOBALS['latex_structure_continued_caption'] = 'latexcontinued';
        $config = Config::getInstance();
        $config->selectedServer['host'] = 'localhost';
        $config->selectedServer['verbose'] = 'verb';

        $relationParameters = RelationParameters::fromArray([
            'db' => 'database',
            'relation' => 'rel',
            'column_info' => 'col',
        ]);
        (new ReflectionProperty(Relation::class, 'cache'))->setValue(null, $relationParameters);

        ob_start();
        $this->assertTrue(
            $this->object->exportStructure(
                'database',
                '',
                'example.com',
                'test',
                'test',
            ),
        );
        $result = ob_get_clean();

        $this->assertIsString($result);

        $this->assertStringContainsString('\\caption{latexstructure} \\label{latexlabel}', $result);

        $this->assertStringContainsString('caption{latexcontinued}', $result);

        // case 4
        $this->assertTrue(
            $this->object->exportStructure(
                'database',
                '',
                'example.com',
                'triggers',
                'test',
            ),
        );
    }

    public function testTexEscape(): void
    {
        $this->assertEquals(
            '\\$\\%\\{foo\\&bar\\}\\#\\_\\^',
            ExportLatex::texEscape('$%{foo&bar}#_^'),
        );
    }
}
