<?php

declare(strict_types=1);

namespace PhpMyAdmin\Tests;

use PhpMyAdmin\Config;
use PhpMyAdmin\Core;
use PhpMyAdmin\DatabaseInterface;
use PhpMyAdmin\Http\ServerRequest;
use PhpMyAdmin\ResponseRenderer;
use PhpMyAdmin\Url;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RequiresPhpExtension;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use stdClass;

use function _pgettext;
use function hash;
use function header;
use function serialize;
use function str_repeat;

#[CoversClass(Core::class)]
class CoreTest extends AbstractTestCase
{
    /**
     * Setup for test cases
     */
    protected function setUp(): void
    {
        parent::setUp();

        parent::setTheme();

        parent::setLanguage();

        DatabaseInterface::$instance = $this->createDatabaseInterface();

        $GLOBALS['server'] = 0;
        $GLOBALS['db'] = '';
        $GLOBALS['table'] = '';
        Config::getInstance()->set('URLQueryEncryption', false);
    }

    /**
     * Test for Core::arrayRead
     */
    public function testArrayRead(): void
    {
        $arr = [
            'int' => 1,
            'str' => 'str_val',
            'arr' => ['val1', 'val2', 'val3'],
            'sarr' => ['arr1' => [1, 2, 3], [3, ['a', 'b', 'c'], 4]],
        ];

        $this->assertEquals(
            Core::arrayRead('int', $arr),
            $arr['int'],
        );

        $this->assertEquals(
            Core::arrayRead('str', $arr),
            $arr['str'],
        );

        $this->assertEquals(
            Core::arrayRead('arr/0', $arr),
            $arr['arr'][0],
        );

        $this->assertEquals(
            Core::arrayRead('arr/1', $arr),
            $arr['arr'][1],
        );

        $this->assertEquals(
            Core::arrayRead('arr/2', $arr),
            $arr['arr'][2],
        );

        $this->assertEquals(
            Core::arrayRead('sarr/arr1/0', $arr),
            $arr['sarr']['arr1'][0],
        );

        $this->assertEquals(
            Core::arrayRead('sarr/arr1/1', $arr),
            $arr['sarr']['arr1'][1],
        );

        $this->assertEquals(
            Core::arrayRead('sarr/arr1/2', $arr),
            $arr['sarr']['arr1'][2],
        );

        $this->assertEquals(
            Core::arrayRead('sarr/0/0', $arr),
            $arr['sarr'][0][0],
        );

        $this->assertEquals(
            Core::arrayRead('sarr/0/1', $arr),
            $arr['sarr'][0][1],
        );

        $this->assertEquals(
            Core::arrayRead('sarr/0/1/2', $arr),
            $arr['sarr'][0][1][2],
        );

        $this->assertEquals(
            Core::arrayRead('sarr/not_exiting/1', $arr),
            null,
        );

        $this->assertEquals(
            Core::arrayRead('sarr/not_exiting/1', $arr, 0),
            0,
        );

        $this->assertEquals(
            Core::arrayRead('sarr/not_exiting/1', $arr, 'default_val'),
            'default_val',
        );
    }

    /**
     * Test for Core::arrayWrite
     */
    public function testArrayWrite(): void
    {
        $arr = [
            'int' => 1,
            'str' => 'str_val',
            'arr' => ['val1', 'val2', 'val3'],
            'sarr' => ['arr1' => [1, 2, 3], [3, ['a', 'b', 'c'], 4]],
        ];

        Core::arrayWrite('int', $arr, 5);
        $this->assertEquals($arr['int'], 5);

        Core::arrayWrite('str', $arr, '_str');
        $this->assertEquals($arr['str'], '_str');

        Core::arrayWrite('arr/0', $arr, 'val_arr_0');
        $this->assertEquals($arr['arr'][0], 'val_arr_0');

        Core::arrayWrite('arr/1', $arr, 'val_arr_1');
        $this->assertEquals($arr['arr'][1], 'val_arr_1');

        Core::arrayWrite('arr/2', $arr, 'val_arr_2');
        $this->assertEquals($arr['arr'][2], 'val_arr_2');

        Core::arrayWrite('sarr/arr1/0', $arr, 'val_sarr_arr_0');
        $this->assertEquals($arr['sarr']['arr1'][0], 'val_sarr_arr_0');

        Core::arrayWrite('sarr/arr1/1', $arr, 'val_sarr_arr_1');
        $this->assertEquals($arr['sarr']['arr1'][1], 'val_sarr_arr_1');

        Core::arrayWrite('sarr/arr1/2', $arr, 'val_sarr_arr_2');
        $this->assertEquals($arr['sarr']['arr1'][2], 'val_sarr_arr_2');

        Core::arrayWrite('sarr/0/0', $arr, 5);
        $this->assertEquals($arr['sarr'][0][0], 5);

        Core::arrayWrite('sarr/0/1/0', $arr, 'e');
        $this->assertEquals($arr['sarr'][0][1][0], 'e');

        Core::arrayWrite('sarr/not_existing/1', $arr, 'some_val');
        $this->assertEquals($arr['sarr']['not_existing'][1], 'some_val');

        Core::arrayWrite('sarr/0/2', $arr, null);
        $this->assertNull($arr['sarr'][0][2]);
    }

    /**
     * Test for Core::arrayRemove
     */
    public function testArrayRemove(): void
    {
        $arr = [
            'int' => 1,
            'str' => 'str_val',
            'arr' => ['val1', 'val2', 'val3'],
            'sarr' => ['arr1' => [1, 2, 3], [3, ['a', 'b', 'c'], 4]],
        ];

        Core::arrayRemove('int', $arr);
        $this->assertArrayNotHasKey('int', $arr);

        Core::arrayRemove('str', $arr);
        $this->assertArrayNotHasKey('str', $arr);

        Core::arrayRemove('arr/0', $arr);
        $this->assertArrayNotHasKey(0, $arr['arr']);

        Core::arrayRemove('arr/1', $arr);
        $this->assertArrayNotHasKey(1, $arr['arr']);

        Core::arrayRemove('arr/2', $arr);
        $this->assertArrayNotHasKey('arr', $arr);

        $tmpArr = $arr;
        Core::arrayRemove('sarr/not_existing/1', $arr);
        $this->assertEquals($tmpArr, $arr);

        Core::arrayRemove('sarr/arr1/0', $arr);
        $this->assertArrayNotHasKey(0, $arr['sarr']['arr1']);

        Core::arrayRemove('sarr/arr1/1', $arr);
        $this->assertArrayNotHasKey(1, $arr['sarr']['arr1']);

        Core::arrayRemove('sarr/arr1/2', $arr);
        $this->assertArrayNotHasKey('arr1', $arr['sarr']);

        Core::arrayRemove('sarr/0/0', $arr);
        $this->assertArrayNotHasKey(0, $arr['sarr'][0]);

        Core::arrayRemove('sarr/0/1/0', $arr);
        $this->assertArrayNotHasKey(0, $arr['sarr'][0][1]);

        Core::arrayRemove('sarr/0/1/1', $arr);
        $this->assertArrayNotHasKey(1, $arr['sarr'][0][1]);

        Core::arrayRemove('sarr/0/1/2', $arr);
        $this->assertArrayNotHasKey(1, $arr['sarr'][0]);

        Core::arrayRemove('sarr/0/2', $arr);

        $this->assertEmpty($arr);
    }

    /**
     * Test for Core::checkPageValidity
     *
     * @param string   $page      Page
     * @param string[] $allowList Allow list
     * @param bool     $include   whether the page is going to be included
     * @param bool     $expected  Expected value
     */
    #[DataProvider('providerTestGotoNowhere')]
    public function testGotoNowhere(string $page, array $allowList, bool $include, bool $expected): void
    {
        $this->assertSame($expected, Core::checkPageValidity($page, $allowList, $include));
    }

    /**
     * Data provider for testGotoNowhere
     *
     * @return array<array{string, string[], bool, bool}>
     */
    public static function providerTestGotoNowhere(): array
    {
        return [
            ['', [], false, false],
            ['', [], true, false],
            ['shell.php', ['index.php'], false, false],
            ['shell.php', ['index.php'], true, false],
            ['index.php?sql.php&test=true', ['index.php'], false, true],
            ['index.php?sql.php&test=true', ['index.php'], true, false],
            ['index.php%3Fsql.php%26test%3Dtrue', ['index.php'], false, true],
            ['index.php%3Fsql.php%26test%3Dtrue', ['index.php'], true, false],
        ];
    }

    /**
     * Test for Core::getRealSize
     *
     * @param string $size     Size
     * @param int    $expected Expected value
     */
    #[DataProvider('providerTestGetRealSize')]
    #[Group('32bit-incompatible')]
    public function testGetRealSize(string $size, int $expected): void
    {
        $this->assertEquals($expected, Core::getRealSize($size));
    }

    /**
     * Data provider for testGetRealSize
     *
     * @return array<array{string, int}>
     */
    public static function providerTestGetRealSize(): array
    {
        return [
            ['0', 0],
            ['1kb', 1024],
            ['1024k', 1024 * 1024],
            ['8m', 8 * 1024 * 1024],
            ['12gb', 12 * 1024 * 1024 * 1024],
            ['1024', 1024],
            ['8000m', 8 * 1000 * 1024 * 1024],
            ['8G', 8 * 1024 * 1024 * 1024],
            ['2048', 2048],
            ['2048K', 2048 * 1024],
            ['2048K', 2048 * 1024],
            ['102400K', 102400 * 1024],
        ];
    }

    /**
     * Test for Core::getPHPDocLink
     */
    public function testGetPHPDocLink(): void
    {
        $lang = _pgettext('PHP documentation language', 'en');
        $this->assertEquals(
            Core::getPHPDocLink('function'),
            'index.php?route=/url&url=https%3A%2F%2Fwww.php.net%2Fmanual%2F'
            . $lang . '%2Ffunction',
        );
    }

    /**
     * Test for Core::linkURL
     *
     * @param string $link URL where to go
     * @param string $url  Expected value
     */
    #[DataProvider('providerTestLinkURL')]
    public function testLinkURL(string $link, string $url): void
    {
        $this->assertEquals(Core::linkURL($link), $url);
    }

    /**
     * Data provider for testLinkURL
     *
     * @return array<array{string, string}>
     */
    public static function providerTestLinkURL(): array
    {
        return [
            ['https://wiki.phpmyadmin.net', 'index.php?route=/url&url=https%3A%2F%2Fwiki.phpmyadmin.net'],
            ['https://wiki.phpmyadmin.net', 'index.php?route=/url&url=https%3A%2F%2Fwiki.phpmyadmin.net'],
            ['wiki.phpmyadmin.net', 'wiki.phpmyadmin.net'],
            ['index.php?db=phpmyadmin', 'index.php?db=phpmyadmin'],
        ];
    }

    #[DataProvider('provideTestIsAllowedDomain')]
    public function testIsAllowedDomain(string $url, bool $expected): void
    {
        $_SERVER['SERVER_NAME'] = 'server.local';
        $this->assertEquals(
            $expected,
            Core::isAllowedDomain($url),
        );
    }

    /**
     * @return array<int, array<int, bool|string>>
     * @psalm-return list<array{string, bool}>
     */
    public static function provideTestIsAllowedDomain(): array
    {
        return [
            ['', false],
            ['//', false],
            ['https://www.phpmyadmin.net/', true],
            ['https://www.phpmyadmin.net:123/', false],
            ['http://duckduckgo.com\\@github.com', false],
            ['https://user:pass@github.com:123/', false],
            ['https://user:pass@github.com/', false],
            ['https://server.local/', true],
            ['./relative/', false],
            ['//wiki.phpmyadmin.net', true],
            ['//www.phpmyadmin.net', true],
            ['//phpmyadmin.net', true],
            ['//demo.phpmyadmin.net', true],
            ['//docs.phpmyadmin.net', true],
            ['//dev.mysql.com', true],
            ['//bugs.mysql.com', true],
            ['//mariadb.org', true],
            ['//mariadb.com', true],
            ['//php.net', true],
            ['//www.php.net', true],
            ['//github.com', true],
            ['//www.github.com', true],
            ['//www.percona.com', true],
            ['//mysqldatabaseadministration.blogspot.com', true],
        ];
    }

    /**
     * Test for unserializing
     *
     * @param string $data     Serialized data
     * @param mixed  $expected Expected result
     */
    #[DataProvider('provideTestSafeUnserialize')]
    public function testSafeUnserialize(string $data, mixed $expected): void
    {
        $this->assertEquals(
            $expected,
            Core::safeUnserialize($data),
        );
    }

    /**
     * Test data provider
     *
     * @return array<array{string, mixed}>
     */
    public static function provideTestSafeUnserialize(): array
    {
        return [
            ['s:6:"foobar";', 'foobar'],
            ['foobar', null],
            ['b:0;', false],
            ['O:1:"a":1:{s:5:"value";s:3:"100";}', null],
            ['O:8:"stdClass":1:{s:5:"field";O:8:"stdClass":0:{}}', null],
            [
                'a:2:{i:0;s:90:"1234567890;a3456789012345678901234567890123456789012'
                . '34567890123456789012345678901234567890";i:1;O:8:"stdClass":0:{}}',
                null,
            ],
            [serialize([1, 2, 3]), [1, 2, 3]],
            [serialize('string""'), 'string""'],
            [serialize(['foo' => 'bar']), ['foo' => 'bar']],
            [serialize(['1', new stdClass(), '2']), null],
        ];
    }

    /**
     * Test for MySQL host sanitizing
     *
     * @param string $host     Test host name
     * @param string $expected Expected result
     */
    #[DataProvider('provideTestSanitizeMySQLHost')]
    public function testSanitizeMySQLHost(string $host, string $expected): void
    {
        $this->assertEquals(
            $expected,
            Core::sanitizeMySQLHost($host),
        );
    }

    /**
     * Test data provider
     *
     * @return array<array{string, string}>
     */
    public static function provideTestSanitizeMySQLHost(): array
    {
        return [
            ['p:foo.bar', 'foo.bar'],
            ['p:p:foo.bar', 'foo.bar'],
            ['bar.baz', 'bar.baz'],
            ['P:example.com', 'example.com'],
        ];
    }

    /**
     * Test for replacing dots.
     */
    public function testReplaceDots(): void
    {
        $this->assertEquals(
            Core::securePath('../../../etc/passwd'),
            './././etc/passwd',
        );
        $this->assertEquals(
            Core::securePath('/var/www/../phpmyadmin'),
            '/var/www/./phpmyadmin',
        );
        $this->assertEquals(
            Core::securePath('./path/with..dots/../../file..php'),
            './path/with.dots/././file.php',
        );
    }

    /**
     * Test for Core::warnMissingExtension
     */
    public function testMissingExtensionFatal(): void
    {
        $_REQUEST = [];
        ResponseRenderer::getInstance()->setAjax(false);

        $ext = 'php_ext';
        $warn = 'The <a href="' . Core::getPHPDocLink('book.' . $ext . '.php')
            . '" target="Documentation"><em>' . $ext
            . '</em></a> extension is missing. Please check your PHP configuration.';

        $this->expectExceptionMessage($warn);

        Core::warnMissingExtension($ext, true);
    }

    /**
     * Test for Core::warnMissingExtension
     */
    public function testMissingExtensionFatalWithExtra(): void
    {
        $_REQUEST = [];
        ResponseRenderer::getInstance()->setAjax(false);

        $ext = 'php_ext';
        $extra = 'Appended Extra String';

        $warn = 'The <a href="' . Core::getPHPDocLink('book.' . $ext . '.php')
            . '" target="Documentation"><em>' . $ext
            . '</em></a> extension is missing. Please check your PHP configuration.'
            . ' ' . $extra;

        $this->expectExceptionMessage($warn);

        Core::warnMissingExtension($ext, true, $extra);
    }

    /**
     * Test for Core::signSqlQuery
     */
    public function testSignSqlQuery(): void
    {
        $_SESSION[' HMAC_secret '] = hash('sha1', 'test');
        $sqlQuery = 'SELECT * FROM `test`.`db` WHERE 1;';
        $signature = Core::signSqlQuery($sqlQuery);
        $hmac = '33371e8680a640dc05944a2a24e6e630d3e9e3dba24464135f2fb954c3a4ffe2';
        $this->assertSame($hmac, $signature, 'The signature must match the computed one');
    }

    /**
     * Test for Core::checkSqlQuerySignature
     */
    public function testCheckSqlQuerySignature(): void
    {
        $_SESSION[' HMAC_secret '] = hash('sha1', 'test');
        $sqlQuery = 'SELECT * FROM `test`.`db` WHERE 1;';
        $hmac = '33371e8680a640dc05944a2a24e6e630d3e9e3dba24464135f2fb954c3a4ffe2';
        $this->assertTrue(Core::checkSqlQuerySignature($sqlQuery, $hmac));
    }

    /**
     * Test for Core::checkSqlQuerySignature
     */
    public function testCheckSqlQuerySignatureFails(): void
    {
        $_SESSION[' HMAC_secret '] = hash('sha1', '132654987gguieunofz');
        $sqlQuery = 'SELECT * FROM `test`.`db` WHERE 1;';
        $hmac = '33371e8680a640dc05944a2a24e6e630d3e9e3dba24464135f2fb954c3a4ffe2';
        $this->assertFalse(Core::checkSqlQuerySignature($sqlQuery, $hmac));
    }

    /**
     * Test for Core::checkSqlQuerySignature
     */
    public function testCheckSqlQuerySignatureFailsBadHash(): void
    {
        $_SESSION[' HMAC_secret '] = hash('sha1', 'test');
        $sqlQuery = 'SELECT * FROM `test`.`db` WHERE 1;';
        $hmac = '3333333380a640dc05944a2a24e6e630d3e9e3dba24464135f2fb954c3eeeeee';
        $this->assertFalse(Core::checkSqlQuerySignature($sqlQuery, $hmac));
    }

    /**
     * Test for Core::checkSqlQuerySignature
     */
    public function testCheckSqlQuerySignatureFailsNoSession(): void
    {
        $_SESSION[' HMAC_secret '] = 'empty';
        $sqlQuery = 'SELECT * FROM `test`.`db` WHERE 1;';
        $hmac = '3333333380a640dc05944a2a24e6e630d3e9e3dba24464135f2fb954c3eeeeee';
        $this->assertFalse(Core::checkSqlQuerySignature($sqlQuery, $hmac));
    }

    /**
     * Test for Core::checkSqlQuerySignature
     */
    public function testCheckSqlQuerySignatureFailsFromAnotherSession(): void
    {
        $_SESSION[' HMAC_secret '] = hash('sha1', 'firstSession');
        $sqlQuery = 'SELECT * FROM `test`.`db` WHERE 1;';
        $hmac = Core::signSqlQuery($sqlQuery);
        $this->assertTrue(Core::checkSqlQuerySignature($sqlQuery, $hmac));
        $_SESSION[' HMAC_secret '] = hash('sha1', 'secondSession');
        // Try to use the token (hmac) from the previous session
        $this->assertFalse(Core::checkSqlQuerySignature($sqlQuery, $hmac));
    }

    /**
     * Test for Core::checkSqlQuerySignature
     */
    public function testCheckSqlQuerySignatureFailsBlowfishSecretChanged(): void
    {
        $config = Config::getInstance();
        $config->settings['blowfish_secret'] = '';
        $_SESSION[' HMAC_secret '] = hash('sha1', 'firstSession');
        $sqlQuery = 'SELECT * FROM `test`.`db` WHERE 1;';
        $hmac = Core::signSqlQuery($sqlQuery);
        $this->assertTrue(Core::checkSqlQuerySignature($sqlQuery, $hmac));
        $config->settings['blowfish_secret'] = str_repeat('a', 32);
        // Try to use the previous HMAC signature
        $this->assertFalse(Core::checkSqlQuerySignature($sqlQuery, $hmac));

        $config->settings['blowfish_secret'] = str_repeat('a', 32);
        // Generate the HMAC signature to check that it works
        $hmac = Core::signSqlQuery($sqlQuery);
        // Must work now, (good secret and blowfish_secret)
        $this->assertTrue(Core::checkSqlQuerySignature($sqlQuery, $hmac));
    }

    public function testPopulateRequestWithEncryptedQueryParams(): void
    {
        $_SESSION = [];
        $config = Config::getInstance();
        $config->set('URLQueryEncryption', true);
        $config->set('URLQueryEncryptionSecretKey', str_repeat('a', 32));

        $_GET = ['pos' => '0', 'eq' => Url::encryptQuery('{"db":"test_db","table":"test_table"}')];
        $_REQUEST = $_GET;

        $request = $this->createStub(ServerRequest::class);
        $request->method('getQueryParams')->willReturn($_GET);
        $request->method('getParsedBody')->willReturn(null);
        $request->method('withQueryParams')->willReturnSelf();
        $request->method('withParsedBody')->willReturnSelf();

        Core::populateRequestWithEncryptedQueryParams($request);

        $expected = ['pos' => '0', 'db' => 'test_db', 'table' => 'test_table'];

        $this->assertEquals($expected, $_GET);
        $this->assertEquals($expected, $_REQUEST);
    }

    /**
     * @param string[] $encrypted
     * @param string[] $decrypted
     */
    #[DataProvider('providerForTestPopulateRequestWithEncryptedQueryParamsWithInvalidParam')]
    public function testPopulateRequestWithEncryptedQueryParamsWithInvalidParam(
        array $encrypted,
        array $decrypted,
    ): void {
        $_SESSION = [];
        $config = Config::getInstance();
        $config->set('URLQueryEncryption', true);
        $config->set('URLQueryEncryptionSecretKey', str_repeat('a', 32));

        $_GET = $encrypted;
        $_REQUEST = $encrypted;

        $request = $this->createStub(ServerRequest::class);
        $request->method('getQueryParams')->willReturn($_GET);
        $request->method('getParsedBody')->willReturn(null);
        $request->method('withQueryParams')->willReturnSelf();
        $request->method('withParsedBody')->willReturnSelf();

        Core::populateRequestWithEncryptedQueryParams($request);

        $this->assertEquals($decrypted, $_GET);
        $this->assertEquals($decrypted, $_REQUEST);
    }

    /** @return array<int, array<int, array<string, string|mixed[]>>> */
    public static function providerForTestPopulateRequestWithEncryptedQueryParamsWithInvalidParam(): array
    {
        return [[[], []], [['eq' => []], []], [['eq' => ''], []], [['eq' => 'invalid'], []]];
    }

    #[PreserveGlobalState(false)]
    #[Group('ext-xdebug')]
    #[RequiresPhpExtension('xdebug')]
    #[RunInSeparateProcess]
    public function testDownloadHeader(): void
    {
        Config::getInstance()->set('PMA_USR_BROWSER_AGENT', 'FIREFOX');

        header('Cache-Control: private, max-age=10800');

        Core::downloadHeader('test.sql', 'text/x-sql', 100, false);

        // phpcs:disable SlevomatCodingStandard.Namespaces.ReferenceUsedNamesOnly.ReferenceViaFullyQualifiedName
        $headersList = \xdebug_get_headers();
        // phpcs:enable

        $this->assertContains('Cache-Control: private, max-age=10800', $headersList);
        $this->assertContains('Content-Description: File Transfer', $headersList);
        $this->assertContains('Content-Disposition: attachment; filename="test.sql"', $headersList);
        $this->assertContains('Content-type: text/x-sql;charset=UTF-8', $headersList);
        $this->assertContains('Content-Transfer-Encoding: binary', $headersList);
        $this->assertContains('Content-Length: 100', $headersList);
        $this->assertNotContains('Content-Encoding: gzip', $headersList);
    }

    #[PreserveGlobalState(false)]
    #[Group('ext-xdebug')]
    #[RequiresPhpExtension('xdebug')]
    #[RunInSeparateProcess]
    public function testDownloadHeader2(): void
    {
        Config::getInstance()->set('PMA_USR_BROWSER_AGENT', 'FIREFOX');

        header('Cache-Control: private, max-age=10800');

        Core::downloadHeader('test.sql.gz', 'application/x-gzip', 0, false);

        // phpcs:disable SlevomatCodingStandard.Namespaces.ReferenceUsedNamesOnly.ReferenceViaFullyQualifiedName
        $headersList = \xdebug_get_headers();
        // phpcs:enable

        $this->assertContains('Cache-Control: private, max-age=10800', $headersList);
        $this->assertContains('Content-Description: File Transfer', $headersList);
        $this->assertContains('Content-Disposition: attachment; filename="test.sql.gz"', $headersList);
        $this->assertContains('Content-Type: application/x-gzip', $headersList);
        $this->assertNotContains('Content-Encoding: gzip', $headersList);
        $this->assertContains('Content-Transfer-Encoding: binary', $headersList);
        $this->assertNotContains('Content-Length: 0', $headersList);
    }
}
