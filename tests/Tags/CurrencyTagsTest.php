<?php

namespace Aerni\Snipcart\Tests\Tags;

use Statamic\Facades\Site;
use Aerni\Snipcart\Tests\TestCase;
use Aerni\Snipcart\Facades\Currency;
use Aerni\Snipcart\Tags\CurrencyTags;

class CurrencyTagsTest extends TestCase
{
    protected $tag;

    public function setUp(): void
    {
        parent::setUp();

        $this->tag = resolve(CurrencyTags::class);
    }

    /** @test */
    public function it_returns_the_currency_code()
    {
        $code = Currency::from(Site::current())->code();

        $this->assertEquals($this->tag->code(), $code);
    }

    /** @test */
    public function it_returns_the_currency_name()
    {
        $name = Currency::from(Site::current())->name();

        $this->assertEquals($this->tag->name(), $name);
    }

    /** @test */
    public function it_returns_the_currency_symbol()
    {
        $symbol = Currency::from(Site::current())->symbol();

        $this->assertEquals($this->tag->symbol(), $symbol);
    }
}
