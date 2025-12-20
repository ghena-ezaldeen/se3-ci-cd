<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ChangeAccountStateTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    /** @test */
    public function admin_can_freeze_account()
    {
        // arrange
        $admin = User::factory()->create();
        $adminRole = Role::factory()->admin()->create();

        $permission = \App\Models\Permission::firstOrCreate(
            ['name' => 'account.changeState'],
            ['description' => 'Change account state']
        );

        $adminRole->permissions()->syncWithoutDetaching([$permission->id]);

        $admin->roles()->attach($adminRole->id);

        $account = Account::factory()->create([
            'state' => 'active',
        ]);

        // act
        $response = $this->actingAs($admin)
            ->postJson(route('accounts.changeState', ['account' => $account->id]), [
                'action' => 'freeze',
            ]);

        // assert
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Account state updated',
                'state' => 'frozen',
            ]);

        $this->assertDatabaseHas('accounts', [
            'id' => $account->id,
            'state' => 'frozen',
        ]);
    }
}
