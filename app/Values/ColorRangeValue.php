<?php

namespace App\Values;

class ColorRangeValue
{
    public function __construct(
        private ?string $darkColor,
        private ?string $lightColor
    ) {
    }

    public static function make(?string $darkColor, ?string $lightColor): self
    {
        return new self($darkColor, $lightColor);
    }

    public static function jsonUnserialize(?string $payload): ?self
    {
        if (is_null($payload)) {
            return null;
        }

        $colorRange = json_decode($payload, true);

        if (isset($colorRange['dark_color']) && isset($colorRange['light_color'])) {
            return self::make($colorRange['dark_color'], $colorRange['light_color']);
        }

        return null;
    }

    public static function jsonSerialize(array $colorRange): ?string
    {
        if (count($colorRange) <= 1) {
            return null;
        }

        if (! $colorRange[0] || ! $colorRange[1]) {
            return null;
        }

        return json_encode(self::make((string) $colorRange[0], (string) $colorRange[1])->response());
    }

    public function response(): ?array
    {
        if (empty($this->darkColor) && empty($this->lightColor)) {
            return null;
        }

        return [
            'dark_color' => $this->darkColor,
            'light_color' => $this->lightColor,
        ];
    }
}
