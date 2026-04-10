<?php

namespace App\Domain\CreditCards\Actions;

use App\Domain\CreditCards\DTOs\CreateCreditCardData;
use App\Domain\CreditCards\Models\CreditCard;
use App\Models\User;

class CreateCreditCardAction
{
    public function execute(User $user, CreateCreditCardData $data): CreditCard
    {
        return $user->creditCards()->create($data->toArray());
    }
}
