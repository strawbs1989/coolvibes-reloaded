<?php

declare(strict_types=1);

namespace PhpMyAdmin\Tests\Import;

use PhpMyAdmin\Config;
use PhpMyAdmin\DatabaseInterface;
use PhpMyAdmin\Import\Import;
use PhpMyAdmin\Tests\AbstractTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

use function time;

use const PHP_INT_MAX;

#[CoversClass(Import::class)]
class ImportTest extends AbstractTestCase
{
    private Import $import;

    /**
     * Prepares environment for the test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        DatabaseInterface::$instance = $this->createDatabaseInterface();
        $GLOBALS['server'] = 0;
        Config::getInstance()->settings['ServerDefault'] = '';
        $GLOBALS['complete_query'] = null;
        $GLOBALS['display_query'] = null;
        $GLOBALS['skip_queries'] = null;
        $GLOBALS['max_sql_len'] = null;
        $GLOBALS['sql_query_disabled'] = null;
        $GLOBALS['executed_queries'] = null;
        $this->import = new Import();
    }

    /**
     * Test for checkTimeout
     */
    public function testCheckTimeout(): void
    {
        //Reinit values.
        $GLOBALS['timestamp'] = time();
        $GLOBALS['maximum_time'] = 0;
        $GLOBALS['timeout_passed'] = false;

        $this->assertFalse($this->import->checkTimeout());

        //Reinit values.
        $GLOBALS['timestamp'] = time();
        $GLOBALS['maximum_time'] = 0;
        $GLOBALS['timeout_passed'] = true;

        $this->assertFalse($this->import->checkTimeout());

        //Reinit values.
        $GLOBALS['timestamp'] = time();
        $GLOBALS['maximum_time'] = 30;
        $GLOBALS['timeout_passed'] = true;

        $this->assertTrue($this->import->checkTimeout());

        //Reinit values.
        $GLOBALS['timestamp'] = time() - 15;
        $GLOBALS['maximum_time'] = 30;
        $GLOBALS['timeout_passed'] = false;

        $this->assertFalse($this->import->checkTimeout());

        //Reinit values.
        $GLOBALS['timestamp'] = time() - 60;
        $GLOBALS['maximum_time'] = 30;
        $GLOBALS['timeout_passed'] = false;

        $this->assertTrue($this->import->checkTimeout());
    }

    /**
     * Test for lookForUse
     */
    public function testLookForUse(): void
    {
        $this->assertEquals(
            [null, null],
            $this->import->lookForUse(null, null, null),
        );

        $this->assertEquals(
            ['myDb', null],
            $this->import->lookForUse(null, 'myDb', null),
        );

        $this->assertEquals(
            ['myDb', true],
            $this->import->lookForUse(null, 'myDb', true),
        );

        $this->assertEquals(
            ['myDb', true],
            $this->import->lookForUse('select 1 from myTable', 'myDb', true),
        );

        $this->assertEquals(
            ['anotherDb', true],
            $this->import->lookForUse('use anotherDb', 'myDb', false),
        );

        $this->assertEquals(
            ['anotherDb', true],
            $this->import->lookForUse('use anotherDb', 'myDb', true),
        );

        $this->assertEquals(
            ['anotherDb', true],
            $this->import->lookForUse('use `anotherDb`;', 'myDb', true),
        );
    }

    /**
     * Test for getColumnAlphaName
     *
     * @param string $expected Expected result of the function
     * @param int    $num      The column number
     */
    #[DataProvider('provGetColumnAlphaName')]
    public function testGetColumnAlphaName(string $expected, int $num): void
    {
        $this->assertEquals($expected, $this->import->getColumnAlphaName($num));
    }

    /**
     * Data provider for testGetColumnAlphaName
     *
     * @return mixed[]
     */
    public static function provGetColumnAlphaName(): array
    {
        return [['A', 1], ['Z', 0], ['AA', 27], ['AZ', 52], ['BA', 53], ['BB', 54]];
    }

    /**
     * Test for getColumnNumberFromName
     *
     * @param int    $expected Expected result of the function
     * @param string $name     column name(i.e. "A", or "BC", etc.)
     */
    #[DataProvider('provGetColumnNumberFromName')]
    public function testGetColumnNumberFromName(int $expected, string $name): void
    {
        $this->assertEquals($expected, $this->import->getColumnNumberFromName($name));
    }

    /**
     * Data provider for testGetColumnNumberFromName
     *
     * @return mixed[]
     */
    public static function provGetColumnNumberFromName(): array
    {
        return [[1, 'A'], [26, 'Z'], [27, 'AA'], [52, 'AZ'], [53, 'BA'], [54, 'BB']];
    }

    /**
     * Test for getDecimalPrecision
     *
     * @param int    $expected Expected result of the function
     * @param string $size     Size of field
     */
    #[DataProvider('provGetDecimalPrecision')]
    public function testGetDecimalPrecision(int $expected, string $size): void
    {
        $this->assertEquals($expected, $this->import->getDecimalPrecision($size));
    }

    /**
     * Data provider for testGetDecimalPrecision
     *
     * @return mixed[]
     */
    public static function provGetDecimalPrecision(): array
    {
        return [[2, '2,1'], [6, '6,2'], [6, '6,0'], [16, '16,2']];
    }

    /**
     * Test for getDecimalScale
     *
     * @param int    $expected Expected result of the function
     * @param string $size     Size of field
     */
    #[DataProvider('provGetDecimalScale')]
    public function testGetDecimalScale(int $expected, string $size): void
    {
        $this->assertEquals($expected, $this->import->getDecimalScale($size));
    }

    /**
     * Data provider for testGetDecimalScale
     *
     * @return mixed[]
     */
    public static function provGetDecimalScale(): array
    {
        return [[1, '2,1'], [2, '6,2'], [0, '6,0'], [20, '30,20']];
    }

    /**
     * Test for getDecimalSize
     *
     * @param mixed[] $expected Expected result of the function
     * @param string  $cell     Cell content
     */
    #[DataProvider('provGetDecimalSize')]
    public function testGetDecimalSize(array $expected, string $cell): void
    {
        $this->assertEquals($expected, $this->import->getDecimalSize($cell));
    }

    /**
     * Data provider for testGetDecimalSize
     *
     * @return mixed[]
     */
    public static function provGetDecimalSize(): array
    {
        return [[[2, 1, '2,1'], '2.1'], [[2, 1, '2,1'], '6.2'], [[3, 1, '3,1'], '10.0'], [[4, 2, '4,2'], '30.20']];
    }

    /**
     * Test for detectType
     *
     * @param int         $expected Expected result of the function
     * @param int|null    $type     Last cumulative column type (VARCHAR or INT or
     *                              BIGINT or DECIMAL or NONE)
     * @param string|null $cell     String representation of the cell for which a
     *                              best-fit type is to be determined
     */
    #[DataProvider('provDetectType')]
    public function testDetectType(int $expected, int|null $type, string|null $cell): void
    {
        $this->assertEquals($expected, $this->import->detectType($type, $cell));
    }

    /**
     * Data provider for testDetectType
     *
     * @return mixed[]
     */
    public static function provDetectType(): array
    {
        $data = [
            [Import::NONE, null, 'NULL'],
            [Import::NONE, Import::NONE, 'NULL'],
            [Import::INT, Import::INT, 'NULL'],
            [Import::VARCHAR, Import::VARCHAR, 'NULL'],
            [Import::VARCHAR, null, null],
            [Import::VARCHAR, Import::INT, null],
            [Import::INT, Import::INT, '10'],
            [Import::DECIMAL, Import::DECIMAL, '10.2'],
            [Import::DECIMAL, Import::INT, '10.2'],
            [Import::VARCHAR, Import::VARCHAR, 'test'],
            [Import::VARCHAR, Import::INT, 'test'],
        ];

        if (PHP_INT_MAX > 2147483647) {
            $data[] = [Import::BIGINT, Import::BIGINT, '2147483648'];
            $data[] = [Import::BIGINT, Import::INT, '2147483648'];
        } else {
            // To be fixed ?
            // Can not detect a BIGINT since the value is over PHP_INT_MAX
            $data[] = [Import::VARCHAR, Import::BIGINT, '2147483648'];
            $data[] = [Import::VARCHAR, Import::INT, '2147483648'];
        }

        return $data;
    }

    /**
     * Test for checkIfRollbackPossible
     */
    public function testPMACheckIfRollbackPossible(): void
    {
        $GLOBALS['db'] = 'PMA';

        $sqlQuery = 'UPDATE `table_1` AS t1, `table_2` t2 SET `table_1`.`id` = `table_2`.`id` WHERE 1';

        $this->assertTrue($this->import->checkIfRollbackPossible($sqlQuery));
    }

    /**
     * Data provider for testSkipByteOrderMarksFromContents
     *
     * @return mixed[][]
     */
    public static function providerContentWithByteOrderMarks(): array
    {
        return [
            ["\xEF\xBB\xBF blabla上海", ' blabla上海'],
            ["\xEF\xBB\xBF blabla", ' blabla'],
            ["\xEF\xBB\xBF blabla\xEF\xBB\xBF", " blabla\xEF\xBB\xBF"],
            ["\xFE\xFF blabla", ' blabla'],
            ["\xFE\xFF blabla\xFE\xFF", " blabla\xFE\xFF"],
            ["\xFF\xFE blabla", ' blabla'],
            ["\xFF\xFE blabla\xFF\xFE", " blabla\xFF\xFE"],
            [
                "\xEF\xBB\xBF\x44\x52\x4F\x50\x20\x54\x41\x42\x4C\x45\x20\x49\x46\x20\x45\x58\x49\x53\x54\x53",
                'DROP TABLE IF EXISTS',
            ],
        ];
    }

    /**
     * Test for skipByteOrderMarksFromContents
     *
     * @param string $input         The contents to strip BOM
     * @param string $cleanContents The contents cleaned
     */
    #[DataProvider('providerContentWithByteOrderMarks')]
    public function testSkipByteOrderMarksFromContents(string $input, string $cleanContents): void
    {
        $this->assertEquals($cleanContents, $this->import->skipByteOrderMarksFromContents($input));
    }

    /**
     * Test for runQuery
     */
    public function testRunQuery(): void
    {
        $GLOBALS['run_query'] = true;
        $sqlData = [];

        $this->import->runQuery('SELECT 1', $sqlData);

        $this->assertSame([], $sqlData);
        $this->assertSame('', $GLOBALS['sql_query']);
        $this->assertNull($GLOBALS['complete_query']);
        $this->assertNull($GLOBALS['display_query']);

        $this->import->runQuery('SELECT 2', $sqlData);

        $this->assertSame(['SELECT 1;'], $sqlData);
        $this->assertSame('SELECT 1;', $GLOBALS['sql_query']);
        $this->assertSame('SELECT 1;', $GLOBALS['complete_query']);
        $this->assertSame('SELECT 1;', $GLOBALS['display_query']);

        $this->import->runQuery('', $sqlData);

        $this->assertSame(['SELECT 1;', 'SELECT 2;'], $sqlData);

        $this->assertSame('SELECT 2;', $GLOBALS['sql_query']);
        $this->assertSame('SELECT 1;SELECT 2;', $GLOBALS['complete_query']);
        $this->assertSame('SELECT 1;SELECT 2;', $GLOBALS['display_query']);
    }
}
