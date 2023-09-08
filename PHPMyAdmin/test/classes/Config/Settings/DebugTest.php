<?php

declare(strict_types=1);

namespace PhpMyAdmin\Tests\Config\Settings;

use PhpMyAdmin\Config\Settings\Debug;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(Debug::class)]
class DebugTest extends TestCase
{
    #[DataProvider('booleanWithDefaultFalseProvider')]
    public function testSql(mixed $actual, bool $expected): void
    {
        $debug = new Debug(['sql' => $actual]);
        $debugArray = $debug->asArray();
        $this->assertSame($expected, $debug->sql);
        $this->assertSame($expected, $debugArray['sql']);
    }

    #[DataProvider('booleanWithDefaultFalseProvider')]
    public function testSqllog(mixed $actual, bool $expected): void
    {
        $debug = new Debug(['sqllog' => $actual]);
        $debugArray = $debug->asArray();
        $this->assertSame($expected, $debug->sqllog);
        $this->assertSame($expected, $debugArray['sqllog']);
    }

    #[DataProvider('booleanWithDefaultFalseProvider')]
    public function testDemo(mixed $actual, bool $expected): void
    {
        $debug = new Debug(['demo' => $actual]);
        $debugArray = $debug->asArray();
        $this->assertSame($expected, $debug->demo);
        $this->assertSame($expected, $debugArray['demo']);
    }

    #[DataProvider('booleanWithDefaultFalseProvider')]
    public function testSimple2fa(mixed $actual, bool $expected): void
    {
        $debug = new Debug(['simple2fa' => $actual]);
        $debugArray = $debug->asArray();
        $this->assertSame($expected, $debug->simple2fa);
        $this->assertSame($expected, $debugArray['simple2fa']);
    }

    /** @return iterable<string, array{mixed, bool}> */
    public static function booleanWithDefaultFalseProvider(): iterable
    {
        yield 'null value' => [null, false];
        yield 'valid value' => [false, false];
        yield 'valid value 2' => [true, true];
        yield 'valid value with type coercion' => [1, true];
    }
}
