<?php

namespace App\Domain\Tags\Actions;

use App\Domain\Tags\DTOs\CreateTagRuleData;
use App\Domain\Tags\Models\TagRule;

class CreateTagRuleAction
{
    public function execute(CreateTagRuleData $data): TagRule
    {
        return TagRule::create($data->toArray());
    }
}
