<?php

namespace App\Support;

class Embed
{
    /** @return array{name: string, value: string, inline: bool} */
    public static function field(string $name, string $value, bool $inline = false): array
    {
        return [
            'name' => $name,
            'value' => $value,
            'inline' => $inline,
        ];
    }

    /** @return array{name: string, value: string, inline: bool} */
    public static function inlineField(string $name, string $value): array
    {
        return self::field($name, $value, true);
    }
}
