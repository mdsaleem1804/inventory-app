<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_cannot_access_products_index(): void
    {
        $staff = User::factory()->create(['role' => User::ROLE_STAFF]);

        $response = $this->actingAs($staff)->get(route('products.index'));

        $response->assertForbidden();
    }

    public function test_staff_can_access_sales_pages(): void
    {
        $staff = User::factory()->create(['role' => User::ROLE_STAFF]);

        $this->actingAs($staff)
            ->get(route('sales.index'))
            ->assertOk();

        $this->actingAs($staff)
            ->get(route('sales.create'))
            ->assertOk();
    }

    public function test_manager_can_access_products_index(): void
    {
        $manager = User::factory()->create(['role' => User::ROLE_MANAGER]);

        $response = $this->actingAs($manager)->get(route('products.index'));

        $response->assertOk();
    }

    public function test_admin_can_create_user_with_role(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Created User',
            'email' => 'created.user@example.com',
            'password' => 'SecretPass@123',
            'password_confirmation' => 'SecretPass@123',
            'role' => User::ROLE_MANAGER,
        ]);

        $response->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'created.user@example.com',
            'role' => User::ROLE_MANAGER,
        ]);
    }

    public function test_manager_cannot_create_user(): void
    {
        $manager = User::factory()->create(['role' => User::ROLE_MANAGER]);

        $response = $this->actingAs($manager)->post(route('admin.users.store'), [
            'name' => 'Blocked User',
            'email' => 'blocked.user@example.com',
            'password' => 'SecretPass@123',
            'password_confirmation' => 'SecretPass@123',
            'role' => User::ROLE_STAFF,
        ]);

        $response->assertForbidden();

        $this->assertDatabaseMissing('users', [
            'email' => 'blocked.user@example.com',
        ]);
    }

    public function test_admin_can_update_user_name_and_password(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $targetUser = User::factory()->create([
            'name' => 'Old Name',
            'password' => Hash::make('OldPass@123'),
            'role' => User::ROLE_STAFF,
        ]);

        $response = $this->actingAs($admin)->patch(route('admin.users.update-account', $targetUser), [
            'name' => 'New Name',
            'email' => 'updated.user@example.com',
            'role' => User::ROLE_MANAGER,
            'password' => 'NewPass@123',
            'password_confirmation' => 'NewPass@123',
        ]);

        $response->assertRedirect(route('admin.users.index'));

        $targetUser->refresh();
        $this->assertSame('New Name', $targetUser->name);
        $this->assertSame('updated.user@example.com', $targetUser->email);
        $this->assertSame(User::ROLE_MANAGER, $targetUser->role);
        $this->assertTrue(Hash::check('NewPass@123', $targetUser->password));
    }

    public function test_manager_cannot_update_user_account(): void
    {
        $manager = User::factory()->create(['role' => User::ROLE_MANAGER]);
        $targetUser = User::factory()->create(['role' => User::ROLE_STAFF]);

        $response = $this->actingAs($manager)->patch(route('admin.users.update-account', $targetUser), [
            'name' => 'Blocked Edit',
            'password' => 'NoAccess@123',
            'password_confirmation' => 'NoAccess@123',
        ]);

        $response->assertForbidden();
    }
}
