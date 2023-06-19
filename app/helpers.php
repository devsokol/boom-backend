<?php

if (! function_exists('__choice')) {
    function __choice(string $key, int $count, array $replace = []): string
    {
        return trans_choice(__($key, $replace), $count);
    }
}

if (! function_exists('isExistsTraitInClass')) {
    function isExistsTraitInClass(string $target, object $class): bool
    {
        return in_array(
            $target,
            array_keys((new \ReflectionClass($class))->getTraits())
        );
    }
}

if (! function_exists('generateSequenceNumbers')) {
    function generateSequenceNumbers(int $from, int $to): array
    {
        $sequence = [];

        for ($num = $from; $num <= $to; $num++) {
            $sequence[] = $num;
        }

        return $sequence;
    }
}
