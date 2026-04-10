<?php

namespace App\Domain\Tags\Strategies;

use App\Domain\Tags\Contracts\TaggingStrategyInterface;
use App\Domain\Transactions\Models\Transaction;
use Illuminate\Support\Collection;

class RuleBasedTaggingStrategy implements TaggingStrategyInterface
{
    public function resolve(Transaction $transaction, Collection $rules): array
    {
        $tagIds = [];

        foreach ($rules as $rule) {
            $fieldValue = match ($rule->field) {
                'description' => $transaction->description ?? '',
                'merchant' => $transaction->merchant ?? '',
                default => '',
            };

            $matches = match ($rule->operator) {
                'contains' => str_contains(strtolower($fieldValue), strtolower($rule->value)),
                'starts_with' => str_starts_with(strtolower($fieldValue), strtolower($rule->value)),
                'equals' => strtolower($fieldValue) === strtolower($rule->value),
                default => false,
            };

            if ($matches) {
                $tagIds[] = $rule->tag_id;
            }
        }

        return array_unique($tagIds);
    }
}
