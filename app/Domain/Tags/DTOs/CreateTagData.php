<?php

namespace App\Domain\Tags\DTOs;

use Spatie\LaravelData\Data;

class CreateTagData extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $color = null,
        public readonly ?string $icon = null,
    ) {}

    public static function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'color' => ['nullable', 'string'],
            'icon' => ['nullable', 'string'],
        ];
    }
}
