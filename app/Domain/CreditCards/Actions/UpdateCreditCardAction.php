<?php

namespace App\Domain\CreditCards\Actions;

use App\Domain\CreditCards\Models\CreditCard;

class UpdateCreditCardAction
{
    public function execute(CreditCard $card, array $data): CreditCard
    {
        $card->update($data);

        return $card->fresh();
    }
}
