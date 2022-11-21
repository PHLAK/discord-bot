<?php

namespace App\Support;

class Embed
{
    /** Build a field array. */
    public static function field(string $name, string $value, bool $inline = false): array
    {
        return [
            'name' => $name,
            'value' => $value,
            'inline' => $inline,
        ];
    }

    /** Build an inline field array. */
    public static function inlineField(string $name, string $value): array
    {
        return self::field($name, $value, true);
    }
}
