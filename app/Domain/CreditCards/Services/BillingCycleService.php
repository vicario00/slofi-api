<?php

namespace App\Domain\CreditCards\Services;

use App\Domain\CreditCards\Models\CreditCard;
use Illuminate\Support\Carbon;

class BillingCycleService
{
    public function lastCutoffDate(CreditCard $card, ?Carbon $today = null): Carbon
    {
        $today ??= Carbon::today();

        // Clamp cutoff_day to the number of days in the current month
        $day = min($card->cutoff_day, $today->daysInMonth);
        $cutoff = $today->copy()->setDay($day);

        // If cutoff is in the future (or today), go back one month
        if ($cutoff->greaterThan($today)) {
            $cutoff->subMonthNoOverflow();
            // Re-clamp after month change (e.g., March 31 → Feb 28)
            $cutoff->setDay(min($card->cutoff_day, $cutoff->daysInMonth));
        }

        return $cutoff;
    }

    public function currentPeriodBalance(CreditCard $card): array
    {
        $periodStart = $this->lastCutoffDate($card);
        $today = Carbon::today();

        // Next cutoff date
        $periodEnd = $periodStart->copy()->addMonthNoOverflow();
        $periodEnd->setDay(min($card->cutoff_day, $periodEnd->daysInMonth));

        $balance = $card->transactions()
            ->where('type', 'expense')
            ->whereDate('transacted_at', '>', $periodStart)
            ->whereDate('transacted_at', '<=', $today)
            ->sum('amount');

        return [
            'balance' => round((float) $balance, 2),
            'period_start' => $periodStart->toDateString(),
            'period_end' => $periodEnd->toDateString(),
        ];
    }
}
