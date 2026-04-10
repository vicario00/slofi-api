<?php

namespace Tests\Feature\Accounts;

use App\Domain\Accounts\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AccountTest extends TestCase
{
    use RefreshDatabase;

    private function createAccount(User $user, array $overrides = []): Account
    {
        return Account::create(array_merge([
            'user_id' => $user->id,
            'name' => 'Checking Account',
            'type' => 'checking',
            'balance' => '1000.00',
            'currency' => 'MXN',
            'icon' => null,
            'color' => '#000000',
        ], $overrides));
    }

    public function test_can_create_account(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/accounts', [
            'name' => 'My Savings',
            'type' => 'savings',
            'balance' => 500.00,
            'currency' => 'MXN',
        ]);

        $response->assertStatus(201)
            ->assertJsonFragment(['name' => 'My Savings'])
            ->assertJsonFragment(['type' => 'savings']);

        $this->assertDatabaseHas('accounts', ['name' => 'My Savings', 'user_id' => $user->id]);
    }

    public function test_can_list_accounts(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->createAccount($user, ['name' => 'Account A']);
        $this->createAccount($user, ['name' => 'Account B']);

        $response = $this->getJson('/api/accounts');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function test_can_update_account(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $account = $this->createAccount($user);

        $response = $this->putJson("/api/accounts/{$account->id}", [
            'name' => 'Updated Name',
            'type' => 'savings',
            'balance' => '2000.00',
            'currency' => 'USD',
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Updated Name']);
    }

    public function test_can_delete_account(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $account = $this->createAccount($user);

        $response = $this->deleteJson("/api/accounts/{$account->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('accounts', ['id' => $account->id]);
    }

    public function test_cannot_access_other_users_account(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();

        $account = $this->createAccount($owner);

        Sanctum::actingAs($intruder);

        $response = $this->getJson("/api/accounts/{$account->id}");

        $response->assertStatus(403);
    }

    public function test_balance_is_correct_type(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $account = $this->createAccount($user, ['balance' => '1234.56']);

        $response = $this->getJson("/api/accounts/{$account->id}");

        $response->assertStatus(200);

        $balance = $response->json('data.balance');
        // Balance should be numeric (either string decimal or number)
        $this->assertTrue(
            is_numeric($balance),
            'Balance should be numeric, got: '.gettype($balance).' = '.var_export($balance, true)
        );
    }
}
