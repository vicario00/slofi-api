<?php

namespace App\Domain\Accounts\DTOs;

use Spatie\LaravelData\Data;

class CreateAccountData extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly string $type,
        public readonly float $balance = 0,
        public readonly string $currency = 'MXN',
        public readonly ?string $icon = null,
        public readonly ?string $color = null,
    ) {}

    public static function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:checking,savings,cash,investment'],
            'balance' => ['numeric', 'min:0'],
            'currency' => ['string', 'size:3'],
            'icon' => ['nullable', 'string'],
            'color' => ['nullable', 'string', 'max:7'],
        ];
    }
}
