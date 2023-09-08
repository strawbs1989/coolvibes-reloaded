<?php

declare(strict_types=1);

namespace PhpMyAdmin\Tests\Controllers\Table;

use PhpMyAdmin\Config;
use PhpMyAdmin\Controllers\Table\IndexesController;
use PhpMyAdmin\DatabaseInterface;
use PhpMyAdmin\DbTableExists;
use PhpMyAdmin\Html\Generator;
use PhpMyAdmin\Html\MySQLDocumentation;
use PhpMyAdmin\Index;
use PhpMyAdmin\Message;
use PhpMyAdmin\Table;
use PhpMyAdmin\Table\Indexes;
use PhpMyAdmin\Template;
use PhpMyAdmin\Tests\AbstractTestCase;
use PhpMyAdmin\Tests\Stubs\ResponseRenderer as ResponseStub;
use PhpMyAdmin\Url;
use PHPUnit\Framework\Attributes\CoversClass;
use ReflectionMethod;

use function __;
use function sprintf;

#[CoversClass(IndexesController::class)]
class IndexesControllerTest extends AbstractTestCase
{
    /**
     * Setup function for test cases
     */
    protected function setUp(): void
    {
        parent::setUp();

        parent::setTheme();

        /**
         * SET these to avoid undefined index error
         */
        $GLOBALS['server'] = 1;
        $GLOBALS['db'] = 'db';
        $GLOBALS['table'] = 'table';
        $GLOBALS['text_dir'] = 'ltr';
        $config = Config::getInstance();
        $config->selectedServer['pmadb'] = '';
        $config->selectedServer['DisableIS'] = false;
        $GLOBALS['urlParams'] = ['db' => 'db', 'server' => 1];
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        DatabaseInterface::$instance = null;
    }

    /**
     * Tests for displayFormAction()
     */
    public function testDisplayFormAction(): void
    {
        $dbi = $this->getMockBuilder(DatabaseInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $indexs = [
            ['Schema' => 'Schema1', 'Key_name' => 'Key_name1', 'Column_name' => 'Column_name1'],
            ['Schema' => 'Schema2', 'Key_name' => 'Key_name2', 'Column_name' => 'Column_name2'],
            ['Schema' => 'Schema3', 'Key_name' => 'Key_name3', 'Column_name' => 'Column_name3'],
        ];

        $dbi->expects($this->any())->method('getTableIndexes')
            ->willReturn($indexs);

        DatabaseInterface::$instance = $dbi;

        $table = $this->getMockBuilder(Table::class)
            ->disableOriginalConstructor()
            ->getMock();
        $table->expects($this->any())->method('getStatusInfo')
            ->willReturn('');
        $table->expects($this->any())->method('isView')
            ->willReturn(false);
        $table->expects($this->any())->method('getNameAndTypeOfTheColumns')
            ->willReturn(['field_name' => 'field_type']);

        $dbi->expects($this->any())->method('getTable')
            ->willReturn($table);

        $response = new ResponseStub();
        $index = new Index();
        $template = new Template();

        $method = new ReflectionMethod(IndexesController::class, 'displayForm');

        $ctrl = new IndexesController(
            $response,
            $template,
            $dbi,
            new Indexes($response, $template, $dbi),
            new DbTableExists($dbi),
        );

        $_POST['create_index'] = true;
        $_POST['added_fields'] = 3;
        $method->invoke($ctrl, $index);
        $html = $response->getHTMLResult();

        //Url::getHiddenInputs
        $this->assertStringContainsString(
            Url::getHiddenInputs(
                ['db' => 'db', 'table' => 'table', 'create_index' => 1],
            ),
            $html,
        );

        $docHtml = Generator::showHint(
            Message::notice(
                __(
                    '"PRIMARY" <b>must</b> be the name of and <b>only of</b> a primary key!',
                ),
            )->getMessage(),
        );
        $this->assertStringContainsString($docHtml, $html);

        $this->assertStringContainsString(
            MySQLDocumentation::show('ALTER_TABLE'),
            $html,
        );

        $this->assertStringContainsString(
            sprintf(__('Add %s column(s) to index'), 1),
            $html,
        );

        //$field_name & $field_type
        $this->assertStringContainsString('field_name', $html);
        $this->assertStringContainsString('field_type', $html);
    }
}
