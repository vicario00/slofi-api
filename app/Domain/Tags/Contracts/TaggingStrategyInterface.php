<?php

namespace App\Domain\Tags\Contracts;

use App\Domain\Tags\Models\TagRule;
use App\Domain\Transactions\Models\Transaction;
use Illuminate\Support\Collection;

interface TaggingStrategyInterface
{
    /**
     * Resolve which tag IDs should be attached to the transaction.
     *
     * @param  Collection<int, TagRule>  $rules
     * @return int[]
     */
    public function resolve(Transaction $transaction, Collection $rules): array;
}
