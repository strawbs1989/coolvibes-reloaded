<?php

declare(strict_types=1);

namespace PhpMyAdmin\Tests\Html;

use PhpMyAdmin\Config;
use PhpMyAdmin\Html\MySQLDocumentation;
use PhpMyAdmin\Tests\AbstractTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(MySQLDocumentation::class)]
class MySQLDocumentationTest extends AbstractTestCase
{
    public function testShowDocumentation(): void
    {
        $GLOBALS['server'] = '99';
        Config::getInstance()->settings['ServerDefault'] = 1;

        $this->assertEquals(
            '<a href="index.php?route=/url&url=https%3A%2F%2Fdocs.phpmyadmin.net%2Fen'
            . '%2Flatest%2Fpage.html%23anchor" target="documentation"><img src="themes/dot.gif"'
            . ' title="Documentation" alt="Documentation" class="icon ic_b_help"></a>',
            MySQLDocumentation::showDocumentation('page', 'anchor'),
        );
    }
}
