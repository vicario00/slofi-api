<?php

namespace App\Domain\Accounts\Actions;

use App\Domain\Accounts\Models\Account;

class UpdateAccountAction
{
    public function execute(Account $account, array $data): Account
    {
        // balance is not directly updatable
        unset($data['balance']);
        $account->update($data);

        return $account->fresh();
    }
}
