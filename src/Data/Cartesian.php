<?php

namespace Aerni\Snipcart\Data;

class Cartesian
{
    public static function build(array $set): array
    {
        if (! $set) {
            return [[]];
        }

        $subset = array_shift($set);
        $cartesianSubset = self::build($set);

        $result = [];

        foreach ($subset as $value) {
            foreach ($cartesianSubset as $p) {
                array_unshift($p, $value);
                $result[] = $p;
            }
        }

        return $result;
    }
}
