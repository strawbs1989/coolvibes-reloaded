<?php

declare(strict_types=1);

namespace PhpMyAdmin\Tests\Config\Forms;

use PhpMyAdmin\Config;
use PhpMyAdmin\Config\ConfigFile;
use PhpMyAdmin\Config\Forms\BaseForm;
use PhpMyAdmin\Config\Forms\BaseFormList;
use PhpMyAdmin\Config\Forms\Page;
use PhpMyAdmin\Config\Forms\Page\PageFormList;
use PhpMyAdmin\Config\Forms\Setup;
use PhpMyAdmin\Config\Forms\Setup\SetupFormList;
use PhpMyAdmin\Config\Forms\User;
use PhpMyAdmin\Config\Forms\User\UserFormList;
use PhpMyAdmin\Tests\AbstractTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(BaseFormList::class)]
#[CoversClass(PageFormList::class)]
#[CoversClass(SetupFormList::class)]
#[CoversClass(UserFormList::class)]
class FormListTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        parent::setGlobalConfig();

        $GLOBALS['server'] = 1;
    }

    /**
     * Tests for preferences forms.
     *
     * @param string $class  Class to test
     * @param string $prefix Returned class prefix
     * @psalm-param class-string<BaseFormList> $class
     * @psalm-param class-string<BaseForm> $prefix
     */
    #[DataProvider('formObjects')]
    public function testForms(string $class, string $prefix): void
    {
        $cf = new ConfigFile(Config::getInstance()->baseSettings);

        /* Static API */
        $this->assertTrue($class::isValid('Export'));
        $this->assertEquals($prefix, $class::get('Export'));
        foreach ($class::getAllFormNames() as $form) {
            $formClass = $class::get($form);
            $this->assertNotNull($formClass);
            $this->assertNotNull($formClass::getName());
        }

        $this->assertContains('Export/texytext_columns', $class::getFields());

        /* Instance handling */
        $forms = new $class($cf);
        $this->assertInstanceOf(BaseFormList::class, $forms);
        $this->assertFalse($forms->process());
        $forms->fixErrors();
        $this->assertFalse($forms->hasErrors());
        $this->assertEquals('', $forms->displayErrors());
    }

    /**
     * @return string[][]
     * @psalm-return array{array{class-string<BaseFormList>, class-string<BaseForm>}}
     */
    public static function formObjects(): array
    {
        return [
            [User\UserFormList::class, User\ExportForm::class],
            [Page\PageFormList::class, Page\ExportForm::class],
            [Setup\SetupFormList::class, Setup\ExportForm::class],
        ];
    }
}
