<?php

declare(strict_types=1);

namespace PhpMyAdmin\Tests\Config;

use PhpMyAdmin\Config;
use PhpMyAdmin\Config\ConfigFile;
use PhpMyAdmin\Config\Form;
use PhpMyAdmin\Config\FormDisplay;
use PhpMyAdmin\Tests\AbstractTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

use function function_exists;
use function gettype;

#[CoversClass(FormDisplay::class)]
class FormDisplayTest extends AbstractTestCase
{
    protected FormDisplay $object;

    /**
     * Configures global environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        parent::setTheme();

        parent::setGlobalConfig();

        $GLOBALS['server'] = 0;
        $this->object = new FormDisplay(new ConfigFile());
        Form::resetGroupCounter();
    }

    /**
     * tearDown for test cases
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->object);
    }

    /**
     * Test for FormDisplay::registerForm
     */
    #[Group('medium')]
    public function testRegisterForm(): void
    {
        $reflection = new ReflectionClass(FormDisplay::class);

        $attrForms = $reflection->getProperty('forms');

        $array = ['Servers' => ['1' => ['test' => 1, 1 => ':group:end']]];

        $this->object->registerForm('pma_testform', $array, 2);
        $forms = $attrForms->getValue($this->object);
        $this->assertInstanceOf(Form::class, $forms['pma_testform']);

        $attrSystemPaths = $reflection->getProperty('systemPaths');

        $this->assertEquals(
            ['Servers/2/test' => 'Servers/1/test', 'Servers/2/:group:end:0' => 'Servers/1/:group:end:0'],
            $attrSystemPaths->getValue($this->object),
        );

        $attrTranslatedPaths = $reflection->getProperty('translatedPaths');

        $this->assertEquals(
            ['Servers/2/test' => 'Servers-2-test', 'Servers/2/:group:end:0' => 'Servers-2-:group:end:0'],
            $attrTranslatedPaths->getValue($this->object),
        );
    }

    /**
     * Test for FormDisplay::process
     */
    #[Group('medium')]
    public function testProcess(): void
    {
        $this->assertFalse(
            $this->object->process(true, true),
        );

        $this->object = $this->getMockBuilder(FormDisplay::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['save'])
            ->getMock();

        $attrForms = new ReflectionProperty(FormDisplay::class, 'forms');
        $attrForms->setValue($this->object, [1, 2, 3]);

        $this->object->expects($this->once())
            ->method('save')
            ->with([0, 1, 2], false)
            ->willReturn(true);

        $this->assertTrue(
            $this->object->process(false, false),
        );

        $attrForms->setValue($this->object, []);

        $this->assertFalse(
            $this->object->process(false, false),
        );
    }

    /**
     * Test for FormDisplay::displayErrors
     */
    public function testDisplayErrors(): void
    {
        $reflection = new ReflectionClass(FormDisplay::class);

        $attrIsValidated = $reflection->getProperty('isValidated');
        $attrIsValidated->setValue($this->object, true);

        $attrIsValidated = $reflection->getProperty('errors');
        $attrIsValidated->setValue($this->object, []);

        $result = $this->object->displayErrors();

        $this->assertSame($result, '');

        $arr = ['Servers/1/test' => ['e1'], 'foobar' => ['e2', 'e3']];

        $sysArr = ['Servers/1/test' => 'Servers/1/test2'];

        $attrSystemPaths = $reflection->getProperty('systemPaths');
        $attrSystemPaths->setValue($this->object, $sysArr);

        $attrIsValidated->setValue($this->object, $arr);

        $result = $this->object->displayErrors();

        $this->assertStringContainsString('<dt>Servers/1/test2</dt>', $result);
        $this->assertStringContainsString('<dd>e1</dd>', $result);
        $this->assertStringContainsString('<dt>Form_foobar</dt>', $result);
        $this->assertStringContainsString('<dd>e2</dd>', $result);
        $this->assertStringContainsString('<dd>e3</dd>', $result);
    }

    /**
     * Test for FormDisplay::fixErrors
     */
    public function testFixErrors(): void
    {
        $reflection = new ReflectionClass(FormDisplay::class);

        $attrIsValidated = $reflection->getProperty('isValidated');
        $attrIsValidated->setValue($this->object, true);

        $attrIsValidated = $reflection->getProperty('errors');
        $attrIsValidated->setValue($this->object, []);

        $this->object->fixErrors();

        $arr = ['Servers/1/test' => ['e1'], 'Servers/2/test' => ['e2', 'e3'], 'Servers/3/test' => []];

        $sysArr = ['Servers/1/test' => 'Servers/1/host'];

        $attrSystemPaths = $reflection->getProperty('systemPaths');
        $attrSystemPaths->setValue($this->object, $sysArr);

        $attrIsValidated->setValue($this->object, $arr);

        $this->object->fixErrors();

        $this->assertEquals(
            ['Servers' => ['1' => ['test' => 'localhost']]],
            $_SESSION['ConfigFile0'],
        );
    }

    /**
     * Test for FormDisplay::validateSelect
     */
    public function testValidateSelect(): void
    {
        $attrValidateSelect = new ReflectionMethod(FormDisplay::class, 'validateSelect');

        $arr = ['foo' => 'var'];
        $value = 'foo';
        $this->assertTrue(
            $attrValidateSelect->invokeArgs(
                $this->object,
                [&$value, $arr],
            ),
        );

        $arr = ['' => 'foobar'];
        $value = '';
        $this->assertTrue(
            $attrValidateSelect->invokeArgs(
                $this->object,
                [&$value, $arr],
            ),
        );
        $this->assertEquals(
            'string',
            gettype($value),
        );

        $arr = [0 => 'foobar'];
        $value = 0;
        $this->assertTrue(
            $attrValidateSelect->invokeArgs(
                $this->object,
                [&$value, $arr],
            ),
        );

        $arr = ['1' => 'foobar'];
        $value = 0;
        $this->assertFalse(
            $attrValidateSelect->invokeArgs(
                $this->object,
                [&$value, $arr],
            ),
        );
    }

    /**
     * Test for FormDisplay::hasErrors
     */
    public function testHasErrors(): void
    {
        $this->assertFalse($this->object->hasErrors());

        (new ReflectionProperty(FormDisplay::class, 'errors'))->setValue(
            $this->object,
            [1, 2],
        );

        $this->assertTrue($this->object->hasErrors());
    }

    /**
     * Test for FormDisplay::getDocLink
     */
    public function testGetDocLink(): void
    {
        $this->assertEquals(
            'index.php?route=/url&url='
            . 'https%3A%2F%2Fdocs.phpmyadmin.net%2Fen%2Flatest%2Fconfig.html%23cfg_Servers_3_test_2_',
            $this->object->getDocLink('Servers/3/test/2/'),
        );

        $this->assertEquals(
            '',
            $this->object->getDocLink('Import'),
        );

        $this->assertEquals(
            '',
            $this->object->getDocLink('Export'),
        );
    }

    /**
     * Test for FormDisplay::getOptName
     */
    public function testGetOptName(): void
    {
        $method = new ReflectionMethod(FormDisplay::class, 'getOptName');

        $this->assertEquals(
            'Servers_',
            $method->invoke($this->object, 'Servers/1/'),
        );

        $this->assertEquals(
            'Servers_23_',
            $method->invoke($this->object, 'Servers/1/23/'),
        );
    }

    /**
     * Test for FormDisplay::loadUserprefsInfo
     */
    public function testLoadUserprefsInfo(): void
    {
        $method = new ReflectionMethod(FormDisplay::class, 'loadUserprefsInfo');

        $attrUserprefs = new ReflectionProperty(FormDisplay::class, 'userprefsDisallow');

        $method->invoke($this->object, null);
        $this->assertEquals(
            [],
            $attrUserprefs->getValue($this->object),
        );
    }

    /**
     * Test for FormDisplay::setComments
     */
    public function testSetComments(): void
    {
        $method = new ReflectionMethod(FormDisplay::class, 'setComments');

        // recoding
        $opts = ['values' => []];
        $opts['values']['iconv'] = 'testIconv';
        $opts['values']['mb'] = 'testMB';
        $opts['comment'] = null;
        $opts['comment_warning'] = null;

        $expect = $opts;

        $method->invokeArgs(
            $this->object,
            ['RecodingEngine', &$opts],
        );

        $expect['comment'] = '';
        if (! function_exists('iconv')) {
            $expect['values']['iconv'] .= ' (unavailable)';
            $expect['comment'] = '"iconv" requires iconv extension';
        }

        $expect['comment_warning'] = 1;

        $this->assertEquals($expect, $opts);

        // ZipDump, GZipDump, BZipDump
        $method->invokeArgs(
            $this->object,
            ['ZipDump', &$opts],
        );

        $comment = '';
        if (! function_exists('zip_open')) {
            $comment = 'Compressed import will not work due to missing function zip_open.';
        }

        if (! function_exists('gzcompress')) {
            $comment .= ($comment !== '' ? '; ' : '') . 'Compressed export will not work ' .
            'due to missing function gzcompress.';
        }

        $this->assertEquals($comment, $opts['comment']);

        $this->assertTrue($opts['comment_warning']);

        $method->invokeArgs(
            $this->object,
            ['GZipDump', &$opts],
        );

        $comment = '';
        if (! function_exists('gzopen')) {
            $comment = 'Compressed import will not work due to missing function gzopen.';
        }

        if (! function_exists('gzencode')) {
            $comment .= ($comment !== '' ? '; ' : '') . 'Compressed export will not work ' .
            'due to missing function gzencode.';
        }

        $this->assertEquals($comment, $opts['comment']);

        $this->assertTrue($opts['comment_warning']);

        $method->invokeArgs(
            $this->object,
            ['BZipDump', &$opts],
        );

        $comment = '';
        if (! function_exists('bzopen')) {
            $comment = 'Compressed import will not work due to missing function bzopen.';
        }

        if (! function_exists('bzcompress')) {
            $comment .= ($comment !== '' ? '; ' : '') . 'Compressed export will not work ' .
            'due to missing function bzcompress.';
        }

        $this->assertEquals($comment, $opts['comment']);

        $this->assertTrue($opts['comment_warning']);

        $config = Config::getInstance();
        $config->set('is_setup', false);

        $config->settings['MaxDbList'] = 10;
        $config->settings['MaxTableList'] = 10;
        $config->settings['QueryHistoryMax'] = 10;

        $method->invokeArgs(
            $this->object,
            ['MaxDbList', &$opts],
        );

        $this->assertEquals('maximum 10', $opts['comment']);

        $method->invokeArgs(
            $this->object,
            ['MaxTableList', &$opts],
        );

        $this->assertEquals('maximum 10', $opts['comment']);

        $method->invokeArgs(
            $this->object,
            ['QueryHistoryMax', &$opts],
        );

        $this->assertEquals('maximum 10', $opts['comment']);
    }
}
