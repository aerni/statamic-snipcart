<?php

namespace Aerni\Snipcart\Facades;

use Aerni\Snipcart\Repositories\ConfigRepository;
use Illuminate\Support\Facades\Facade;

/**
 * @method static string apiKey()
 * @method static string currency()
 *
 * @see \Aerni\Snipcart\Repositories\ConfigRepository
 */
class Config extends Facade
{
    protected static function getFacadeAccessor()
    {
        return ConfigRepository::class;
    }
}
