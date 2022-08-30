<?php
use Anthonypauwels\AirTable\AirTable;

if ( function_exists('array_are_identical') ) {
    /**
     * Check if two array are the same
     *
     * @param array $array_a
     * @param array $array_b
     * @return bool
     */
    function array_are_identical(array $array_a, array $array_b):bool
    {
        sort($array_a );
        sort($array_b );

        return $array_a === $array_b;
    }
}