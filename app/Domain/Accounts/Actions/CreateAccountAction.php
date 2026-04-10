<?php

namespace App\Domain\Accounts\Actions;

use App\Domain\Accounts\DTOs\CreateAccountData;
use App\Domain\Accounts\Models\Account;
use App\Models\User;

class CreateAccountAction
{
    public function execute(User $user, CreateAccountData $data): Account
    {
        return $user->accounts()->create($data->toArray());
    }
}
