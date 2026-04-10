<?php

namespace App\Domain\Tags\Actions;

use App\Domain\Tags\DTOs\CreateTagData;
use App\Domain\Tags\Models\Tag;
use App\Models\User;

class CreateTagAction
{
    public function execute(User $user, CreateTagData $data): Tag
    {
        return $user->tags()->create($data->toArray());
    }
}
