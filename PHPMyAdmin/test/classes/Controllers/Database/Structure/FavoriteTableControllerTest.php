<?php

declare(strict_types=1);

namespace PhpMyAdmin\Tests\Controllers\Database\Structure;

use PhpMyAdmin\ConfigStorage\Relation;
use PhpMyAdmin\Controllers\Database\Structure\FavoriteTableController;
use PhpMyAdmin\DatabaseInterface;
use PhpMyAdmin\DbTableExists;
use PhpMyAdmin\RecentFavoriteTable;
use PhpMyAdmin\Template;
use PhpMyAdmin\Tests\AbstractTestCase;
use PhpMyAdmin\Tests\Stubs\DbiDummy;
use PhpMyAdmin\Tests\Stubs\ResponseRenderer as ResponseStub;
use PHPUnit\Framework\Attributes\CoversClass;
use ReflectionClass;

use function json_encode;

#[CoversClass(FavoriteTableController::class)]
class FavoriteTableControllerTest extends AbstractTestCase
{
    protected DatabaseInterface $dbi;

    protected DbiDummy $dummyDbi;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dummyDbi = $this->createDbiDummy();
        $this->dbi = $this->createDatabaseInterface($this->dummyDbi);
        DatabaseInterface::$instance = $this->dbi;
    }

    public function testSynchronizeFavoriteTables(): void
    {
        $GLOBALS['server'] = 1;
        $GLOBALS['text_dir'] = 'ltr';
        $GLOBALS['db'] = 'db';

        $favoriteInstance = $this->getMockBuilder(RecentFavoriteTable::class)
            ->disableOriginalConstructor()
            ->getMock();
        $favoriteInstance->expects($this->exactly(2))
            ->method('getTables')
            ->willReturn([[]], [['db' => 'db', 'table' => 'table']]);

        $class = new ReflectionClass(FavoriteTableController::class);
        $method = $class->getMethod('synchronizeFavoriteTables');

        $controller = new FavoriteTableController(
            new ResponseStub(),
            new Template(),
            new Relation($this->dbi),
            new DbTableExists($this->dbi),
        );

        // The user hash for test
        $user = 'abcdefg';
        $favoriteTable = [$user => [['db' => 'db', 'table' => 'table']]];

        $json = $method->invokeArgs($controller, [$favoriteInstance, $user, $favoriteTable]);

        $this->assertEquals(json_encode($favoriteTable), $json['favoriteTables'] ?? '');
        $this->assertArrayHasKey('list', $json);
    }
}
