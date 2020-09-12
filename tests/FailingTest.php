<?php

namespace Aerni\Snipcart\Tests;

use Aerni\Snipcart\Tests\TestCase;

class FailingTest extends TestCase
{
    /** @test */
    public function it_will_pass()
    {
        $this->assertTrue(true);
    }

    /** @test */
    public function it_will_fail()
    {
        $this->assertTrue(true);
    }
}
