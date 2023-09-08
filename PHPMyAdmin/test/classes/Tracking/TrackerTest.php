<?php

declare(strict_types=1);

namespace PhpMyAdmin\Tests\Tracking;

use PhpMyAdmin\Cache;
use PhpMyAdmin\ColumnFull;
use PhpMyAdmin\Config;
use PhpMyAdmin\ConfigStorage\Relation;
use PhpMyAdmin\ConfigStorage\RelationParameters;
use PhpMyAdmin\DatabaseInterface;
use PhpMyAdmin\Dbal\Connection;
use PhpMyAdmin\Tests\AbstractTestCase;
use PhpMyAdmin\Tests\Stubs\DummyResult;
use PhpMyAdmin\Tracking\Tracker;
use PhpMyAdmin\Util;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionMethod;
use ReflectionProperty;

#[CoversClass(Tracker::class)]
class TrackerTest extends AbstractTestCase
{
    /**
     * Setup function for test cases
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadContainerBuilder();
        DatabaseInterface::$instance = $this->createDatabaseInterface();

        parent::loadDbiIntoContainerBuilder();

        /**
         * SET these to avoid undefined index error
         */
        $GLOBALS['server'] = 1;
        $config = Config::getInstance();
        $config->selectedServer['tracking_add_drop_table'] = '';
        $config->selectedServer['tracking_add_drop_view'] = '';
        $config->selectedServer['tracking_add_drop_database'] = '';
        $config->selectedServer['tracking_default_statements'] = '';
        $config->selectedServer['tracking_version_auto_create'] = '';
        $config->selectedServer['DisableIS'] = false;
        $GLOBALS['export_type'] = null;

        $relationParameters = RelationParameters::fromArray([
            'db' => 'pmadb',
            'trackingwork' => true,
            'tracking' => 'tracking',
        ]);
        (new ReflectionProperty(Relation::class, 'cache'))->setValue(null, $relationParameters);

        $dbi = $this->getMockBuilder(DatabaseInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $dbi->expects($this->any())->method('escapeString')
            ->willReturnArgument(0);
    }

    /**
     * Test for Tracker::enable
     */
    public function testEnabled(): void
    {
        $this->assertFalse(
            Cache::has(Tracker::TRACKER_ENABLED_CACHE_KEY),
        );
        Tracker::enable();
        $this->assertTrue(
            Cache::get(Tracker::TRACKER_ENABLED_CACHE_KEY),
        );
    }

    /**
     * Test for Tracker::isActive()
     */
    public function testIsActive(): void
    {
        $this->assertFalse(
            Cache::has(Tracker::TRACKER_ENABLED_CACHE_KEY),
        );

        $this->assertFalse(
            Tracker::isActive(),
        );

        Tracker::enable();

        $relationParameters = RelationParameters::fromArray([]);
        (new ReflectionProperty(Relation::class, 'cache'))->setValue(null, $relationParameters);

        $this->assertFalse(
            Tracker::isActive(),
        );

        $relationParameters = RelationParameters::fromArray([
            'trackingwork' => true,
            'db' => 'pmadb',
            'tracking' => 'tracking',
        ]);
        (new ReflectionProperty(Relation::class, 'cache'))->setValue(null, $relationParameters);

        $this->assertTrue(
            Tracker::isActive(),
        );
    }

    /**
     * Test for Tracker::isTracked()
     */
    public function testIsTracked(): void
    {
        $this->assertFalse(
            Cache::has(Tracker::TRACKER_ENABLED_CACHE_KEY),
        );

        $this->assertFalse(
            Tracker::isTracked('', ''),
        );

        Tracker::enable();

        $relationParameters = RelationParameters::fromArray([]);
        (new ReflectionProperty(Relation::class, 'cache'))->setValue(null, $relationParameters);

        $this->assertFalse(
            Tracker::isTracked('', ''),
        );

        $relationParameters = RelationParameters::fromArray([
            'trackingwork' => true,
            'db' => 'pmadb',
            'tracking' => 'tracking',
        ]);
        (new ReflectionProperty(Relation::class, 'cache'))->setValue(null, $relationParameters);

        $this->assertTrue(
            Tracker::isTracked('pma_test_db', 'pma_test_table'),
        );

        $this->assertFalse(
            Tracker::isTracked('pma_test_db', 'pma_test_table2'),
        );
    }

    /**
     * Test for Tracker::getLogComment()
     */
    public function testGetLogComment(): void
    {
        $date = Util::date('Y-m-d H:i:s');
        Config::getInstance()->selectedServer['user'] = 'pma_test_user';

        $this->assertEquals(
            '# log ' . $date . " pma_test_user\n",
            Tracker::getLogComment(),
        );
    }

    /**
     * Test for Tracker::createVersion()
     */
    public function testCreateVersion(): void
    {
        $config = Config::getInstance();
        $config->selectedServer['tracking_add_drop_table'] = true;
        $config->selectedServer['tracking_add_drop_view'] = true;
        $config->selectedServer['user'] = 'pma_test_user';

        $resultStub = $this->createMock(DummyResult::class);

        $dbi = $this->getMockBuilder(DatabaseInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        /**
         * set up mock objects
         * passing null to with() for an argument is equivalent
         * to passing $this->anything()
         */

        $getColumnsResult = [
            new ColumnFull('field1', 'int(11)', null, false, 'PRI', null, '', '', ''),
            new ColumnFull('field2', 'text', null, false, '', null, '', '', ''),
        ];
        $dbi->expects($this->once())->method('getColumns')
            ->with('pma_test', 'pma_tbl')
            ->willReturn($getColumnsResult);

        $getIndexesResult = [['Table' => 'pma_tbl', 'Field' => 'field1', 'Key' => 'PRIMARY']];
        $dbi->expects($this->once())->method('getTableIndexes')
            ->with('pma_test', 'pma_tbl')
            ->willReturn($getIndexesResult);

        $showTableStatusQuery = 'SHOW TABLE STATUS FROM `pma_test` WHERE Name = \'pma_tbl\'';
        $useStatement = 'USE `pma_test`';
        $showCreateTableQuery = 'SHOW CREATE TABLE `pma_test`.`pma_tbl`';
        $dbi->expects($this->exactly(3))->method('tryQuery')->willReturnMap([
            [$showTableStatusQuery, Connection::TYPE_USER, DatabaseInterface::QUERY_BUFFERED, true, $resultStub],
            [$useStatement, Connection::TYPE_USER, DatabaseInterface::QUERY_BUFFERED, true, $resultStub],
            [$showCreateTableQuery, Connection::TYPE_USER, DatabaseInterface::QUERY_BUFFERED, true, $resultStub],
        ]);

        $dbi->expects($this->any())->method('query')
            ->willReturn($resultStub);

        $dbi->expects($this->any())->method('getCompatibilities')
            ->willReturn([]);
        $dbi->expects($this->any())->method('quoteString')
            ->willReturnCallback(static fn (string $string): string => "'" . $string . "'");

        DatabaseInterface::$instance = $dbi;
        $this->assertTrue(Tracker::createVersion('pma_test', 'pma_tbl', '1', '11', true));
    }

    /**
     * Test for Tracker::createDatabaseVersion()
     */
    public function testCreateDatabaseVersion(): void
    {
        $config = Config::getInstance();
        $config->selectedServer['tracking_add_drop_table'] = true;
        $config->selectedServer['tracking_add_drop_view'] = true;
        $config->selectedServer['user'] = 'pma_test_user';

        $resultStub = $this->createMock(DummyResult::class);

        $dbi = $this->getMockBuilder(DatabaseInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $expectedMainQuery = '/*NOTRACK*/' . "\n" . 'INSERT INTO `pmadb`.`tracking` (db_name, table_name, version,'
            . ' date_created, date_updated, schema_snapshot, schema_sql, data_sql, tracking)'
            . ' values (\'pma_test\', \'\', \'1\', \'%d-%d-%d %d:%d:%d\', \'%d-%d-%d %d:%d:%d\','
            . ' \'\', \'# log %d-%d-%d %d:%d:%d pma_test_user' . "\n" . 'SHOW DATABASES\', \'' . "\n"
            . '\', \'CREATE DATABASE,ALTER DATABASE,DROP DATABASE\')';

        $dbi->expects($this->exactly(1))
            ->method('queryAsControlUser')
            ->with($this->matches($expectedMainQuery))
            ->willReturn($resultStub);

        $dbi->expects($this->any())->method('quoteString')
            ->willReturnCallback(static fn (string $string): string => "'" . $string . "'");

        DatabaseInterface::$instance = $dbi;
        $this->assertTrue(Tracker::createDatabaseVersion('pma_test', '1', 'SHOW DATABASES'));
    }

    /**
     * Test for Tracker::changeTracking(). This test is also invoked by two
     * other tests: testActivateTracking() and testDeactivateTracking()
     *
     * @param string     $dbname    Database name
     * @param string     $tablename Table name
     * @param string     $version   Version
     * @param string|int $newState  State to change to
     * @param string     $type      Type of test
     */
    public function testChangeTracking(
        string $dbname = 'pma_db',
        string $tablename = 'pma_tbl',
        string $version = '0.1',
        string|int $newState = '1',
        string|null $type = null,
    ): void {
        $dbi = $this->getMockBuilder(DatabaseInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $resultStub = $this->createMock(DummyResult::class);

        $sqlQuery = 'UPDATE `pmadb`.`tracking` SET `tracking_active` = ' . $newState .
        " WHERE `db_name` = '" . $dbname . "'" .
        " AND `table_name` = '" . $tablename . "'" .
        " AND `version` = '" . $version . "'";

        $dbi->expects($this->exactly(1))
            ->method('queryAsControlUser')
            ->with($sqlQuery)
            ->willReturn($resultStub);

        $dbi->expects($this->any())->method('quoteString')
            ->willReturnCallback(static fn (string $string): string => "'" . $string . "'");

        DatabaseInterface::$instance = $dbi;

        if ($type === null) {
            $method = new ReflectionMethod(Tracker::class, 'changeTracking');
            $method->invoke(null, $dbname, $tablename, $version, $newState);
        } elseif ($type === 'activate') {
            Tracker::activateTracking($dbname, $tablename, $version);
        } elseif ($type === 'deactivate') {
            Tracker::deactivateTracking($dbname, $tablename, $version);
        }

        // What's the success criteria? What is the expected result?
    }

    /**
     * Test for Tracker::activateTracking()
     */
    public function testActivateTracking(): void
    {
        $this->testChangeTracking('pma_db', 'pma_tbl', '0.1', 1, 'activate');
    }

    /**
     * Test for Tracker::deactivateTracking()
     */
    public function testDeactivateTracking(): void
    {
        $this->testChangeTracking('pma_db', 'pma_tbl', '0.1', '0', 'deactivate');
    }

    /**
     * Test for Tracker::parseQuery
     *
     * @param string      $query                Query to parse
     * @param string      $type                 Expected type
     * @param string      $identifier           Expected identifier
     * @param string|null $tableName            Expected tablename
     * @param string|null $db                   Expected dbname
     * @param string|null $tableNameAfterRename Expected name after rename
     */
    #[DataProvider('parseQueryData')]
    public function testParseQuery(
        string $query,
        string $type,
        string $identifier,
        string|null $tableName,
        string|null $db = null,
        string|null $tableNameAfterRename = null,
    ): void {
        $result = Tracker::parseQuery($query);

        $this->assertEquals($type, $result['type']);

        $this->assertEquals($identifier, $result['identifier']);

        $this->assertEquals($tableName, $result['tablename']);

        if ($db !== null && $db !== '') {
            $this->assertEquals($db, $GLOBALS['db']);
        }

        if ($tableNameAfterRename === null || $tableNameAfterRename === '') {
            return;
        }

        $this->assertEquals($result['tablename_after_rename'], $tableNameAfterRename);
    }

    /**
     * Data provider for testParseQuery
     *
     * @return mixed[] Test data
     */
    public static function parseQueryData(): array
    {
        // query
        // type
        // identifier
        // table name
        // db (optional)
        // table name after rename (optional)
        $query = [];
        /* TODO: Should test fail when USE is in conjunction with * identifiers?
        $query[] = array(
            " - USE db1;\n- CREATE VIEW db1.v AS SELECT * FROM t;",
            "DDL",
            "CREATE VIEW",
            "v",
            "db1"
        );
        */
        $query[] = ['CREATE VIEW v AS SELECT * FROM t;', 'DDL', 'CREATE VIEW', 'v'];
        $query[] = ['ALTER VIEW db1.v AS SELECT col1, col2, col3, col4 FROM t', 'DDL', 'ALTER VIEW', 'v'];
        $query[] = ['DROP VIEW db1.v;', 'DDL', 'DROP VIEW', 'v'];
        $query[] = ['DROP VIEW IF EXISTS db1.v;', 'DDL', 'DROP VIEW', 'v'];
        $query[] = ['CREATE DATABASE db1;', 'DDL', 'CREATE DATABASE', '', 'db1'];
        $query[] = ['ALTER DATABASE db1;', 'DDL', 'ALTER DATABASE', ''];
        $query[] = ['DROP DATABASE db1;', 'DDL', 'DROP DATABASE', '', 'db1'];
        $query[] = ['CREATE TABLE db1.t1 (c1 INT);', 'DDL', 'CREATE TABLE', 't1'];
        $query[] = ['ALTER TABLE db1.t1 ADD c2 TEXT;', 'DDL', 'ALTER TABLE', 't1'];
        $query[] = ['DROP TABLE db1.t1', 'DDL', 'DROP TABLE', 't1'];
        $query[] = ['DROP TABLE IF EXISTS db1.t1', 'DDL', 'DROP TABLE', 't1'];
        $query[] = ['CREATE INDEX ind ON db1.t1 (c2(10));', 'DDL', 'CREATE INDEX', 't1'];
        $query[] = ['CREATE UNIQUE INDEX ind ON db1.t1 (c2(10));', 'DDL', 'CREATE INDEX', 't1'];
        $query[] = ['CREATE SPATIAL INDEX ind ON db1.t1 (c2(10));', 'DDL', 'CREATE INDEX', 't1'];
        $query[] = ['DROP INDEX ind ON db1.t1;', 'DDL', 'DROP INDEX', 't1'];
        $query[] = ['RENAME TABLE db1.t1 TO db1.t2', 'DDL', 'RENAME TABLE', 't1', '', 't2'];
        $query[] = ['UPDATE db1.t1 SET a = 2', 'DML', 'UPDATE', 't1'];
        $query[] = ['INSERT INTO db1.t1 (a, b, c) VALUES(1, 2, 3)', 'DML', 'INSERT', 't1'];
        $query[] = ['DELETE FROM db1.t1', 'DML', 'DELETE', 't1'];
        $query[] = ['TRUNCATE db1.t1', 'DML', 'TRUNCATE', 't1'];
        $query[] = [
            'create table event(' . "\n"
            . 'eventID varchar(10) not null,' . "\n"
            . 'b char(30),' . "\n"
            . 'c varchar(20),' . "\n"
            . 'd TIME,' . "\n"
            . 'e Date,' . "\n"
            . 'f int,' . "\n"
            . 'g char(70),' . "\n"
            . 'h char(90),' . "\n"
            . 'primary key(eventID)' . "\n"
            . ')' . "\n",
            'DDL',
            'CREATE TABLE',
            null,// switch this to 'event' when sql-parse is fixed
        ];

        return $query;
    }
}
