<?php

declare(strict_types=1);

namespace PhpMyAdmin\Tests\Plugins\Import;

use PhpMyAdmin\Config;
use PhpMyAdmin\DatabaseInterface;
use PhpMyAdmin\File;
use PhpMyAdmin\Plugins\Import\ImportLdi;
use PhpMyAdmin\Tests\AbstractTestCase;
use PhpMyAdmin\Tests\Stubs\DummyResult;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

use function __;

#[CoversClass(ImportLdi::class)]
class ImportLdiTest extends AbstractTestCase
{
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        parent::setUp();

        DatabaseInterface::$instance = $this->createDatabaseInterface();
        $GLOBALS['charset_conversion'] = null;
        $GLOBALS['ldi_terminated'] = null;
        $GLOBALS['ldi_escaped'] = null;
        $GLOBALS['ldi_columns'] = null;
        $GLOBALS['ldi_enclosed'] = null;
        $GLOBALS['ldi_new_line'] = null;
        $GLOBALS['max_sql_len'] = null;
        $GLOBALS['sql_query'] = '';
        $GLOBALS['executed_queries'] = null;
        $GLOBALS['skip_queries'] = null;
        $GLOBALS['run_query'] = null;
        $GLOBALS['go_sql'] = null;
        //setting
        $GLOBALS['server'] = 0;
        $GLOBALS['plugin_param'] = 'table';
        $GLOBALS['finished'] = false;
        $GLOBALS['read_limit'] = 100000000;
        $GLOBALS['offset'] = 0;
        $config = Config::getInstance();
        $config->selectedServer['DisableIS'] = false;

        $GLOBALS['import_file'] = 'test/test_data/db_test_ldi.csv';
        $GLOBALS['import_text'] = 'ImportLdi_Test';
        $GLOBALS['read_multiply'] = 10;
        $GLOBALS['import_type'] = 'csv';

        //setting for Ldi
        $config->settings['Import']['ldi_replace'] = false;
        $config->settings['Import']['ldi_ignore'] = false;
        $config->settings['Import']['ldi_terminated'] = ';';
        $config->settings['Import']['ldi_enclosed'] = '"';
        $config->settings['Import']['ldi_escaped'] = '\\';
        $config->settings['Import']['ldi_new_line'] = 'auto';
        $config->settings['Import']['ldi_columns'] = '';
        $config->settings['Import']['ldi_local_option'] = false;
        $GLOBALS['table'] = 'phpmyadmintest';
    }

    /**
     * Test for getProperties
     */
    #[Group('medium')]
    public function testGetProperties(): void
    {
        $properties = (new ImportLdi())->getProperties();
        $this->assertEquals(
            __('CSV using LOAD DATA'),
            $properties->getText(),
        );
        $this->assertEquals(
            'ldi',
            $properties->getExtension(),
        );
    }

    /**
     * Test for getProperties for ldi_local_option = auto
     */
    #[Group('medium')]
    public function testGetPropertiesAutoLdi(): void
    {
        $dbi = $this->createMock(DatabaseInterface::class);
        DatabaseInterface::$instance = $dbi;

        $resultStub = $this->createMock(DummyResult::class);

        $dbi->expects($this->any())->method('tryQuery')
            ->willReturn($resultStub);

        $resultStub->expects($this->any())->method('numRows')
            ->willReturn(10);

        $resultStub->expects($this->any())->method('fetchValue')
            ->willReturn('ON');

        $config = Config::getInstance();
        $config->settings['Import']['ldi_local_option'] = 'auto';
        $properties = (new ImportLdi())->getProperties();
        $this->assertTrue($config->settings['Import']['ldi_local_option']);
        $this->assertEquals(
            __('CSV using LOAD DATA'),
            $properties->getText(),
        );
        $this->assertEquals(
            'ldi',
            $properties->getExtension(),
        );
    }

    /**
     * Test for doImport
     */
    #[Group('medium')]
    public function testDoImport(): void
    {
        //$sql_query_disabled will show the import SQL detail

        $GLOBALS['sql_query_disabled'] = false;
        $dbi = $this->createMock(DatabaseInterface::class);
        $dbi->expects($this->any())->method('quoteString')
            ->willReturnCallback(static fn (string $string): string => "'" . $string . "'");
        DatabaseInterface::$instance = $dbi;

        $importHandle = new File($GLOBALS['import_file']);
        $importHandle->open();

        //Test function called
        (new ImportLdi())->doImport($importHandle);

        //asset that all sql are executed
        $this->assertStringContainsString(
            'LOAD DATA INFILE \'test/test_data/db_test_ldi.csv\' INTO TABLE `phpmyadmintest`',
            $GLOBALS['sql_query'],
        );

        $this->assertTrue($GLOBALS['finished']);
    }

    /**
     * Test for doImport : invalid import file
     */
    #[Group('medium')]
    public function testDoImportInvalidFile(): void
    {
        $GLOBALS['import_file'] = 'none';

        //Test function called
        (new ImportLdi())->doImport();

        // We handle only some kind of data!
        $this->assertStringContainsString(
            __('This plugin does not support compressed imports!'),
            $GLOBALS['message']->__toString(),
        );

        $this->assertTrue($GLOBALS['error']);
    }

    /**
     * Test for doImport with LDI setting
     */
    #[Group('medium')]
    public function testDoImportLDISetting(): void
    {
        //$sql_query_disabled will show the import SQL detail

        $GLOBALS['sql_query_disabled'] = false;
        $dbi = $this->createMock(DatabaseInterface::class);
        $dbi->expects($this->any())->method('quoteString')
            ->willReturnCallback(static fn (string $string): string => "'" . $string . "'");
        DatabaseInterface::$instance = $dbi;

        $GLOBALS['ldi_local_option'] = true;
        $GLOBALS['ldi_replace'] = true;
        $GLOBALS['ldi_ignore'] = true;
        $GLOBALS['ldi_terminated'] = ',';
        $GLOBALS['ldi_enclosed'] = ')';
        $GLOBALS['ldi_new_line'] = 'newline_mark';
        $GLOBALS['skip_queries'] = true;

        $importHandle = new File($GLOBALS['import_file']);
        $importHandle->open();

        //Test function called
        (new ImportLdi())->doImport($importHandle);

        //asset that all sql are executed
        //replace
        $this->assertStringContainsString(
            'LOAD DATA LOCAL INFILE \'test/test_data/db_test_ldi.csv\' REPLACE INTO TABLE `phpmyadmintest`',
            $GLOBALS['sql_query'],
        );

        //FIELDS TERMINATED
        $this->assertStringContainsString("FIELDS TERMINATED BY ','", $GLOBALS['sql_query']);

        //LINES TERMINATED
        $this->assertStringContainsString("LINES TERMINATED BY 'newline_mark'", $GLOBALS['sql_query']);

        //IGNORE
        $this->assertStringContainsString('IGNORE 1 LINES', $GLOBALS['sql_query']);

        $this->assertTrue($GLOBALS['finished']);
    }
}
