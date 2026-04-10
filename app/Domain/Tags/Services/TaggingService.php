<?php

namespace App\Domain\Tags\Services;

use App\Domain\Tags\Contracts\TaggingStrategyInterface;
use App\Domain\Tags\Models\TagRule;
use App\Domain\Transactions\Models\Transaction;

class TaggingService
{
    public function __construct(
        private readonly TaggingStrategyInterface $strategy,
    ) {}

    public function assign(Transaction $transaction): void
    {
        $rules = TagRule::forUser($transaction->user_id)->get();
        $tagIds = $this->strategy->resolve($transaction, $rules);

        if (! empty($tagIds)) {
            $transaction->tags()->syncWithoutDetaching($tagIds);
        }
    }
}
