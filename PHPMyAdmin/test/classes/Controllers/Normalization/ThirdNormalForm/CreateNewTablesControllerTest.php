<?php

declare(strict_types=1);

namespace PhpMyAdmin\Tests\Controllers\Normalization\ThirdNormalForm;

use PhpMyAdmin\ConfigStorage\Relation;
use PhpMyAdmin\Controllers\Normalization\ThirdNormalForm\CreateNewTablesController;
use PhpMyAdmin\DatabaseInterface;
use PhpMyAdmin\Http\ServerRequest;
use PhpMyAdmin\Normalization;
use PhpMyAdmin\Template;
use PhpMyAdmin\Tests\AbstractTestCase;
use PhpMyAdmin\Tests\Stubs\ResponseRenderer;
use PhpMyAdmin\Transformations;
use PHPUnit\Framework\Attributes\CoversClass;

use function json_encode;

#[CoversClass(CreateNewTablesController::class)]
class CreateNewTablesControllerTest extends AbstractTestCase
{
    public function testDefault(): void
    {
        $GLOBALS['db'] = 'test_db';
        $GLOBALS['table'] = 'test_table';
        $newTables = json_encode([
            'test_table' => [
                'event' => [
                    'pk' => 'eventID',
                    'nonpk' => 'Start_time, DateOfEvent, NumberOfGuests, NameOfVenue, LocationOfVenue',
                ],
                'table2' => ['pk' => 'Start_time', 'nonpk' => 'TypeOfEvent, period'],
            ],
        ]);

        $dbiDummy = $this->createDbiDummy();
        $dbiDummy->addSelectDb('test_db');
        $dbiDummy->addResult(
            'CREATE TABLE `event`'
            . ' SELECT DISTINCT `eventID`, `Start_time`, `DateOfEvent`, `NumberOfGuests`,'
            . ' `NameOfVenue`, `LocationOfVenue` FROM `test_table`;',
            true,
        );
        $dbiDummy->addResult(
            'CREATE TABLE `table2` SELECT DISTINCT `Start_time`, `TypeOfEvent`, `period` FROM `test_table`;',
            true,
        );
        $dbiDummy->addResult('DROP TABLE `test_table`', true);

        $dbi = $this->createDatabaseInterface($dbiDummy);
        DatabaseInterface::$instance = $dbi;
        $response = new ResponseRenderer();
        $template = new Template();
        $request = $this->createStub(ServerRequest::class);
        $request->method('getParsedBodyParam')->willReturnMap([['newTables', null, $newTables]]);

        $controller = new CreateNewTablesController(
            $response,
            $template,
            new Normalization($dbi, new Relation($dbi), new Transformations(), $template),
        );
        $controller($request);

        $this->assertSame([
            'legendText' => 'End of step',
            'headText' => '<h3>The third step of normalization is complete.</h3>',
            'queryError' => false,
            'extra' => '',
        ], $response->getJSONResult());
    }
}
