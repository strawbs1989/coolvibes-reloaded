<?php

declare(strict_types=1);

namespace PhpMyAdmin\Tests\Controllers\Export\Template;

use PhpMyAdmin\Config;
use PhpMyAdmin\ConfigStorage\Relation;
use PhpMyAdmin\ConfigStorage\RelationParameters;
use PhpMyAdmin\Controllers\Export\Template\LoadController;
use PhpMyAdmin\DatabaseInterface;
use PhpMyAdmin\Export\TemplateModel;
use PhpMyAdmin\Http\ServerRequest;
use PhpMyAdmin\Template;
use PhpMyAdmin\Tests\AbstractTestCase;
use PhpMyAdmin\Tests\Stubs\DbiDummy;
use PhpMyAdmin\Tests\Stubs\ResponseRenderer;
use PHPUnit\Framework\Attributes\CoversClass;
use ReflectionProperty;

#[CoversClass(LoadController::class)]
class LoadControllerTest extends AbstractTestCase
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

    public function testLoad(): void
    {
        $GLOBALS['server'] = 1;
        $GLOBALS['text_dir'] = 'ltr';

        $relationParameters = RelationParameters::fromArray([
            'exporttemplateswork' => true,
            'db' => 'db',
            'export_templates' => 'table',
        ]);
        (new ReflectionProperty(Relation::class, 'cache'))->setValue(null, $relationParameters);

        Config::getInstance()->selectedServer['user'] = 'user';

        $response = new ResponseRenderer();
        $request = $this->createStub(ServerRequest::class);
        $request->method('getParsedBodyParam')->willReturn('1');

        (new LoadController(
            $response,
            new Template(),
            new TemplateModel($this->dbi),
            new Relation($this->dbi),
        ))($request);

        $this->assertTrue($response->hasSuccessState());
        $this->assertEquals(['data' => 'data1'], $response->getJSONResult());
    }
}
