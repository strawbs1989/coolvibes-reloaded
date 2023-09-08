<?php

declare(strict_types=1);

namespace PhpMyAdmin\Tests\Config\Settings;

use PhpMyAdmin\Config\Settings\Console;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(Console::class)]
class ConsoleTest extends TestCase
{
    #[DataProvider('booleanWithDefaultFalseProvider')]
    public function testStartHistory(mixed $actual, bool $expected): void
    {
        $console = new Console(['StartHistory' => $actual]);
        $consoleArray = $console->asArray();
        // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
        $this->assertSame($expected, $console->StartHistory);
        $this->assertSame($expected, $consoleArray['StartHistory']);
    }

    /** @return iterable<string, array{mixed, bool}> */
    public static function booleanWithDefaultFalseProvider(): iterable
    {
        yield 'null value' => [null, false];
        yield 'valid value' => [false, false];
        yield 'valid value 2' => [true, true];
        yield 'valid value with type coercion' => [1, true];
    }

    #[DataProvider('booleanWithDefaultFalseProvider')]
    public function testAlwaysExpand(mixed $actual, bool $expected): void
    {
        $console = new Console(['AlwaysExpand' => $actual]);
        $consoleArray = $console->asArray();
        // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
        $this->assertSame($expected, $console->AlwaysExpand);
        $this->assertSame($expected, $consoleArray['AlwaysExpand']);
    }

    #[DataProvider('booleanWithDefaultTrueProvider')]
    public function testCurrentQuery(mixed $actual, bool $expected): void
    {
        $console = new Console(['CurrentQuery' => $actual]);
        $consoleArray = $console->asArray();
        // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
        $this->assertSame($expected, $console->CurrentQuery);
        $this->assertSame($expected, $consoleArray['CurrentQuery']);
    }

    /** @return iterable<string, array{mixed, bool}> */
    public static function booleanWithDefaultTrueProvider(): iterable
    {
        yield 'null value' => [null, true];
        yield 'valid value' => [true, true];
        yield 'valid value 2' => [false, false];
        yield 'valid value with type coercion' => [0, false];
    }

    #[DataProvider('booleanWithDefaultFalseProvider')]
    public function testEnterExecutes(mixed $actual, bool $expected): void
    {
        $console = new Console(['EnterExecutes' => $actual]);
        $consoleArray = $console->asArray();
        // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
        $this->assertSame($expected, $console->EnterExecutes);
        $this->assertSame($expected, $consoleArray['EnterExecutes']);
    }

    #[DataProvider('booleanWithDefaultFalseProvider')]
    public function testDarkTheme(mixed $actual, bool $expected): void
    {
        $console = new Console(['DarkTheme' => $actual]);
        $consoleArray = $console->asArray();
        // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
        $this->assertSame($expected, $console->DarkTheme);
        $this->assertSame($expected, $consoleArray['DarkTheme']);
    }

    #[DataProvider('valuesForModeProvider')]
    public function testMode(mixed $actual, string $expected): void
    {
        $console = new Console(['Mode' => $actual]);
        $consoleArray = $console->asArray();
        // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
        $this->assertSame($expected, $console->Mode);
        $this->assertSame($expected, $consoleArray['Mode']);
    }

    /** @return iterable<string, array{mixed, string}> */
    public static function valuesForModeProvider(): iterable
    {
        yield 'null value' => [null, 'info'];
        yield 'valid value' => ['info', 'info'];
        yield 'valid value 2' => ['show', 'show'];
        yield 'valid value 3' => ['collapse', 'collapse'];
        yield 'invalid value' => ['invalid', 'info'];
    }

    #[DataProvider('valuesForHeightProvider')]
    public function testHeight(mixed $actual, int $expected): void
    {
        $console = new Console(['Height' => $actual]);
        $consoleArray = $console->asArray();
        // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
        $this->assertSame($expected, $console->Height);
        $this->assertSame($expected, $consoleArray['Height']);
    }

    /** @return iterable<string, array{mixed, int}> */
    public static function valuesForHeightProvider(): iterable
    {
        yield 'null value' => [null, 92];
        yield 'valid value' => [1, 1];
        yield 'valid value with type coercion' => ['2', 2];
        yield 'invalid value' => [0, 92];
    }

    #[DataProvider('booleanWithDefaultFalseProvider')]
    public function testGroupQueries(mixed $actual, bool $expected): void
    {
        $console = new Console(['GroupQueries' => $actual]);
        $consoleArray = $console->asArray();
        // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
        $this->assertSame($expected, $console->GroupQueries);
        $this->assertSame($expected, $consoleArray['GroupQueries']);
    }

    #[DataProvider('valuesForOrderByProvider')]
    public function testOrderBy(mixed $actual, string $expected): void
    {
        $console = new Console(['OrderBy' => $actual]);
        $consoleArray = $console->asArray();
        // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
        $this->assertSame($expected, $console->OrderBy);
        $this->assertSame($expected, $consoleArray['OrderBy']);
    }

    /** @return iterable<string, array{mixed, string}> */
    public static function valuesForOrderByProvider(): iterable
    {
        yield 'null value' => [null, 'exec'];
        yield 'valid value' => ['exec', 'exec'];
        yield 'valid value 2' => ['time', 'time'];
        yield 'valid value 3' => ['count', 'count'];
        yield 'invalid value' => ['invalid', 'exec'];
    }

    #[DataProvider('valuesForOrderProvider')]
    public function testOrder(mixed $actual, string $expected): void
    {
        $console = new Console(['Order' => $actual]);
        $consoleArray = $console->asArray();
        // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
        $this->assertSame($expected, $console->Order);
        $this->assertSame($expected, $consoleArray['Order']);
    }

    /** @return iterable<string, array{mixed, string}> */
    public static function valuesForOrderProvider(): iterable
    {
        yield 'null value' => [null, 'asc'];
        yield 'valid value' => ['asc', 'asc'];
        yield 'valid value 2' => ['desc', 'desc'];
        yield 'invalid value' => ['invalid', 'asc'];
    }
}
