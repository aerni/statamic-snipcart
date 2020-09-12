<?php

namespace Aerni\Snipcart\Models;

abstract class Model
{
    protected static $rows;

    abstract protected static function getRows(): array;

    public static function __callStatic($method, $parameters)
    {
        static::initRows();

        return collect(static::$rows)->{$method}(...$parameters);
    }

    private static function initRows(): void
    {
        static::$rows = static::getRows();
    }
}
