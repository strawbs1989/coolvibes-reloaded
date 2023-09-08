<?php

declare(strict_types=1);

namespace PhpMyAdmin\Tests\Controllers\Database\Structure;

use PhpMyAdmin\Config;
use PhpMyAdmin\Controllers\Database\Structure\RealRowCountController;
use PhpMyAdmin\DatabaseInterface;
use PhpMyAdmin\DbTableExists;
use PhpMyAdmin\Http\Factory\ServerRequestFactory;
use PhpMyAdmin\Template;
use PhpMyAdmin\Tests\AbstractTestCase;
use PhpMyAdmin\Tests\Stubs\ResponseRenderer as ResponseStub;
use PHPUnit\Framework\Attributes\CoversClass;

use function json_encode;

#[CoversClass(RealRowCountController::class)]
class RealRowCountControllerTest extends AbstractTestCase
{
    public function testRealRowCount(): void
    {
        $GLOBALS['server'] = 1;
        $GLOBALS['text_dir'] = 'ltr';
        Config::getInstance()->selectedServer['DisableIS'] = true;
        $GLOBALS['db'] = 'world';
        $_REQUEST['table'] = 'City';

        $dbiDummy = $this->createDbiDummy();
        $dbiDummy->addSelectDb('world');
        $dbiDummy->addSelectDb('world');
        $dbi = $this->createDatabaseInterface($dbiDummy);
        DatabaseInterface::$instance = $dbi;

        $response = new ResponseStub();

        $request = ServerRequestFactory::create()->createServerRequest('GET', 'http://example.com/')
            ->withQueryParams(['db' => 'world', 'table' => 'City', 'ajax_request' => '1']);

        (new RealRowCountController($response, new Template(), $dbi, new DbTableExists($dbi)))($request);

        $json = $response->getJSONResult();
        $this->assertEquals('4,079', $json['real_row_count']);

        $_REQUEST['real_row_count_all'] = 'on';

        (new RealRowCountController($response, new Template(), $dbi, new DbTableExists($dbi)))($request);

        $json = $response->getJSONResult();
        $expected = [
            ['table' => 'City', 'row_count' => 4079],
            ['table' => 'Country', 'row_count' => 239],
            ['table' => 'CountryLanguage', 'row_count' => 984],
        ];
        $this->assertEquals(json_encode($expected), $json['real_row_count_all']);

        $dbiDummy->assertAllSelectsConsumed();
    }
}
