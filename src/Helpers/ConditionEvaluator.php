<?php
declare(strict_types = 1);

namespace Core\Helpers;

final class ConditionEvaluator{

    public static function evaluate(
        array $item,
        string $key,
        string $operator,
        mixed $value
    ): bool{
        return match ($operator) {
            '=' => $item[$key] === $value,
            '>' => $item[$key] > $value,
            '<' => $item[$key] < $value,
            '>=' => $item[$key] >= $value,
            '<=' => $item[$key] <= $value,
            '!==' => $item[$key] !== $value,
            default => false,
        };
    }

}