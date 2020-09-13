<?php

namespace Aerni\Snipcart\Tests\Fieldtypes;

use Aerni\Snipcart\Fieldtypes\MoneyFieldtype;
use Aerni\Snipcart\Tests\TestCase;

class MoneyFieldtypeTest extends TestCase
{
    /** @test */
    public function it_can_preload_currency()
    {
        $preload = (new MoneyFieldtype())->preload();
        $site = $preload['default'];

        $this->assertIsArray($site);
        $this->assertArrayHasKey('code', $site);
        $this->assertArrayHasKey('name', $site);
        $this->assertArrayHasKey('symbol', $site);
    }

    /** @test */
    public function it_can_pre_process_money()
    {
        $value = 1999;

        $preProcess = (new MoneyFieldtype())->preProcess($value);

        $this->assertSame('19.99', $preProcess);
    }

    /** @test */
    public function it_can_process_money()
    {
        $value = '19.99';

        $process = (new MoneyFieldtype())->process($value);

        $this->assertSame(1999, $process);
    }

    /** @test */
    public function it_can_augment_money()
    {
        $value = 1999;

        $augment = (new MoneyFieldtype())->augment($value);

        $this->assertSame('$19.99', $augment);
    }
}
