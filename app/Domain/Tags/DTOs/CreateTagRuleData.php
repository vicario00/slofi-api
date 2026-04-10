<?php

namespace App\Domain\Tags\DTOs;

use Spatie\LaravelData\Data;

class CreateTagRuleData extends Data
{
    public function __construct(
        public readonly int $tag_id,
        public readonly string $field,
        public readonly string $operator,
        public readonly string $value,
        public readonly int $priority = 0,
    ) {}

    public static function rules(): array
    {
        return [
            'tag_id' => ['required', 'integer', 'exists:tags,id'],
            'field' => ['required', 'in:description,merchant'],
            'operator' => ['required', 'in:contains,starts_with,equals'],
            'value' => ['required', 'string', 'max:255'],
            'priority' => ['integer'],
        ];
    }
}
