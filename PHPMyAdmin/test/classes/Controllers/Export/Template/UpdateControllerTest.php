<?php

declare(strict_types=1);

namespace PhpMyAdmin\Tests\Controllers\Export\Template;

use PhpMyAdmin\Config;
use PhpMyAdmin\ConfigStorage\Relation;
use PhpMyAdmin\Controllers\Export\Template\UpdateController;
use PhpMyAdmin\DatabaseInterface;
use PhpMyAdmin\Export\TemplateModel;
use PhpMyAdmin\Http\ServerRequest;
use PhpMyAdmin\Template;
use PhpMyAdmin\Tests\AbstractTestCase;
use PhpMyAdmin\Tests\Stubs\DbiDummy;
use PhpMyAdmin\Tests\Stubs\ResponseRenderer;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(UpdateController::class)]
class UpdateControllerTest extends AbstractTestCase
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

    public function testUpdate(): void
    {
        $GLOBALS['server'] = 1;
        $GLOBALS['text_dir'] = 'ltr';

        Config::getInstance()->selectedServer['user'] = 'user';

        $response = new ResponseRenderer();
        $request = $this->createStub(ServerRequest::class);
        $request->method('getParsedBodyParam')->willReturnMap([
            ['templateId', null, '1'],
            ['templateData', '', 'data'],
        ]);

        (new UpdateController(
            $response,
            new Template(),
            new TemplateModel($this->dbi),
            new Relation($this->dbi),
        ))($request);

        $this->assertTrue($response->hasSuccessState());
    }
}
