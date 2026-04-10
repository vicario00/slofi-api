<?php

namespace App\Providers;

use App\Domain\Accounts\Models\Account;
use App\Domain\CreditCards\Models\CreditCard;
use App\Domain\Tags\Contracts\TaggingStrategyInterface;
use App\Domain\Tags\Models\Tag;
use App\Domain\Tags\Strategies\RuleBasedTaggingStrategy;
use App\Policies\AccountPolicy;
use App\Policies\CreditCardPolicy;
use App\Policies\TagPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(TaggingStrategyInterface::class, RuleBasedTaggingStrategy::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Account::class, AccountPolicy::class);
        Gate::policy(CreditCard::class, CreditCardPolicy::class);
        Gate::policy(Tag::class, TagPolicy::class);
    }
}
