<?php

declare(strict_types=1);

namespace PhpMyAdmin\Tests;

use ArrayIterator;
use PhpMyAdmin\Config;
use PhpMyAdmin\DatabaseInterface;
use PhpMyAdmin\Footer;
use PhpMyAdmin\Template;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use ReflectionProperty;

use function json_encode;

#[CoversClass(Footer::class)]
class FooterTest extends AbstractTestCase
{
    /** @var mixed[] store private attributes of PhpMyAdmin\Footer */
    public array $privates = [];

    protected Footer $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        parent::setUp();

        parent::setLanguage();

        parent::setGlobalConfig();

        parent::setTheme();

        DatabaseInterface::$instance = $this->createDatabaseInterface();
        $_SERVER['SCRIPT_NAME'] = 'index.php';
        $GLOBALS['db'] = '';
        $GLOBALS['table'] = '';
        $GLOBALS['text_dir'] = 'ltr';
        $config = Config::getInstance();
        $config->selectedServer['DisableIS'] = false;
        $config->selectedServer['verbose'] = 'verbose host';
        $GLOBALS['server'] = '1';
        $_GET['reload_left_frame'] = '1';
        $GLOBALS['focus_querywindow'] = 'main_pane_left';
        $this->object = new Footer(new Template());
        unset($GLOBALS['error_message']);
        unset($GLOBALS['sql_query']);
        $_POST = [];
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->object);
    }

    /**
     * Test for getDebugMessage
     */
    #[Group('medium')]
    public function testGetDebugMessage(): void
    {
        Config::getInstance()->settings['DBG']['sql'] = true;
        $_SESSION['debug']['queries'] = [
            ['count' => 1, 'time' => 0.2, 'query' => 'SELECT * FROM `pma_bookmark` WHERE 1'],
            ['count' => 1, 'time' => 2.5, 'query' => 'SELECT * FROM `db` WHERE 1'],
        ];

        $this->assertEquals(
            '{"queries":[{"count":1,"time":0.2,"query":"SELECT * FROM `pma_bookmark` WHERE 1"},'
            . '{"count":1,"time":2.5,"query":"SELECT * FROM `db` WHERE 1"}]}',
            $this->object->getDebugMessage(),
        );
    }

    /**
     * Test for removeRecursion
     */
    public function testRemoveRecursion(): void
    {
        $object = (object) [];
        $object->child = (object) [];
        $object->childIterator = new ArrayIterator();
        $object->child->parent = $object;

        $this->callFunction($this->object, Footer::class, 'removeRecursion', [&$object]);
        $this->assertEquals(
            '{"child":{"parent":"***RECURSION***"},"childIterator":"***ITERATOR***"}',
            json_encode($object),
        );
    }

    /**
     * Test for disable
     */
    public function testDisable(): void
    {
        $footer = new Footer(new Template());
        $footer->disable();
        $this->assertEquals(
            '',
            $footer->getDisplay(),
        );
    }

    public function testGetDisplayWhenAjaxIsEnabled(): void
    {
        $template = new Template();
        $footer = new Footer($template);
        $footer->setAjax(true);
        $this->assertEquals(
            $template->render('modals/function_confirm') . "\n"
            . $template->render('modals/add_index') . "\n"
            . $template->render('modals/page_settings') . "\n",
            $footer->getDisplay(),
        );
    }

    /**
     * Test for footer get Scripts
     */
    public function testGetScripts(): void
    {
        $footer = new Footer(new Template());
        $this->assertStringContainsString(
            '<script data-cfasync="false">',
            $footer->getScripts()->getDisplay(),
        );
    }

    /**
     * Test for displaying footer
     */
    #[Group('medium')]
    public function testDisplay(): void
    {
        $footer = new Footer(new Template());
        $this->assertStringContainsString(
            'Open new phpMyAdmin window',
            $footer->getDisplay(),
        );
    }

    /**
     * Test for minimal footer
     */
    public function testMinimal(): void
    {
        $template = new Template();
        $footer = new Footer($template);
        $footer->setMinimal();
        $this->assertEquals(
            $template->render('modals/function_confirm') . "\n"
            . $template->render('modals/add_index') . "\n"
            . $template->render('modals/page_settings')
            . "\n  </div>\n  </body>\n</html>\n",
            $footer->getDisplay(),
        );
    }

    public function testSetAjax(): void
    {
        $isAjax = new ReflectionProperty(Footer::class, 'isAjax');
        $footer = new Footer(new Template());

        $this->assertFalse($isAjax->getValue($footer));
        $footer->setAjax(true);
        $this->assertTrue($isAjax->getValue($footer));
        $footer->setAjax(false);
        $this->assertFalse($isAjax->getValue($footer));
    }
}
