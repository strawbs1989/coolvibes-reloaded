<?php

declare(strict_types=1);

namespace PhpMyAdmin\Tests\Properties\Options\Groups;

use PhpMyAdmin\Properties\Options\Groups\OptionsPropertyMainGroup;
use PhpMyAdmin\Tests\AbstractTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(OptionsPropertyMainGroup::class)]
class OptionsPropertyMainGroupTest extends AbstractTestCase
{
    protected OptionsPropertyMainGroup $object;

    /**
     * Configures global environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->object = new OptionsPropertyMainGroup();
    }

    /**
     * tearDown for test cases
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->object);
    }

    public function testGetItemType(): void
    {
        $this->assertEquals(
            'main',
            $this->object->getItemType(),
        );
    }
}
