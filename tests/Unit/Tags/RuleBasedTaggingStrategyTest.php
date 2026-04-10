<?php

namespace Tests\Unit\Tags;

use App\Domain\Tags\Models\TagRule;
use App\Domain\Tags\Strategies\RuleBasedTaggingStrategy;
use App\Domain\Transactions\Models\Transaction;
use Illuminate\Support\Collection;
use Tests\TestCase;

class RuleBasedTaggingStrategyTest extends TestCase
{
    private RuleBasedTaggingStrategy $strategy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->strategy = new RuleBasedTaggingStrategy;
    }

    /** Helper to create a fake TagRule without hitting the DB */
    private function makeRule(array $attributes): TagRule
    {
        $rule = new TagRule;
        $rule->forceFill($attributes);

        return $rule;
    }

    /** Helper to create a fake Transaction without hitting the DB */
    private function makeTransaction(array $attributes): Transaction
    {
        $tx = new Transaction;
        $tx->forceFill($attributes);

        return $tx;
    }

    public function test_contains_match(): void
    {
        $rule = $this->makeRule([
            'tag_id' => 1,
            'field' => 'description',
            'operator' => 'contains',
            'value' => 'food',
            'priority' => 0,
        ]);

        $transaction = $this->makeTransaction(['description' => 'Fast food', 'merchant' => null]);

        $result = $this->strategy->resolve($transaction, new Collection([$rule]));

        $this->assertContains(1, $result);
    }

    public function test_starts_with_match(): void
    {
        $rule = $this->makeRule([
            'tag_id' => 2,
            'field' => 'merchant',
            'operator' => 'starts_with',
            'value' => 'uber',
            'priority' => 0,
        ]);

        $transaction = $this->makeTransaction(['description' => null, 'merchant' => 'Uber Eats']);

        $result = $this->strategy->resolve($transaction, new Collection([$rule]));

        $this->assertContains(2, $result);
    }

    public function test_equals_match(): void
    {
        $rule = $this->makeRule([
            'tag_id' => 3,
            'field' => 'merchant',
            'operator' => 'equals',
            'value' => 'netflix',
            'priority' => 0,
        ]);

        $transaction = $this->makeTransaction(['description' => null, 'merchant' => 'Netflix']);

        $result = $this->strategy->resolve($transaction, new Collection([$rule]));

        $this->assertContains(3, $result);
    }

    public function test_no_match(): void
    {
        $rule = $this->makeRule([
            'tag_id' => 4,
            'field' => 'description',
            'operator' => 'contains',
            'value' => 'gym',
            'priority' => 0,
        ]);

        $transaction = $this->makeTransaction(['description' => 'Starbucks coffee', 'merchant' => null]);

        $result = $this->strategy->resolve($transaction, new Collection([$rule]));

        $this->assertEmpty($result);
    }

    public function test_multi_tag_dedup(): void
    {
        // Two rules with the same tag_id both matching → tag_id returned only once
        $rule1 = $this->makeRule([
            'tag_id' => 5,
            'field' => 'description',
            'operator' => 'contains',
            'value' => 'food',
            'priority' => 1,
        ]);

        $rule2 = $this->makeRule([
            'tag_id' => 5,
            'field' => 'merchant',
            'operator' => 'contains',
            'value' => 'burger',
            'priority' => 0,
        ]);

        $transaction = $this->makeTransaction(['description' => 'Fast food', 'merchant' => 'Burger King']);

        $result = $this->strategy->resolve($transaction, new Collection([$rule1, $rule2]));

        $this->assertCount(1, $result);
        $this->assertContains(5, $result);
    }

    public function test_priority_ordering(): void
    {
        // Rules already pre-ordered by priority DESC (strategy doesn't sort, it relies on input order)
        $ruleHigh = $this->makeRule([
            'tag_id' => 10,
            'field' => 'description',
            'operator' => 'contains',
            'value' => 'coffee',
            'priority' => 10,
        ]);

        $ruleLow = $this->makeRule([
            'tag_id' => 20,
            'field' => 'description',
            'operator' => 'contains',
            'value' => 'coffee',
            'priority' => 1,
        ]);

        $transaction = $this->makeTransaction(['description' => 'coffee shop', 'merchant' => null]);

        // Pass rules in DESC priority order (as TaggingService does via scopeForUser)
        $result = $this->strategy->resolve($transaction, new Collection([$ruleHigh, $ruleLow]));

        // Both match — result should contain both, but higher priority first
        $this->assertCount(2, $result);
        $this->assertEquals(10, $result[0]);
        $this->assertEquals(20, $result[1]);
    }
}
