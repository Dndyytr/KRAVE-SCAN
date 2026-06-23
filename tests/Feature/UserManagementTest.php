<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    private Role $adminRole;

    private Role $cashierRole;

    private Branch $branch1;

    private Branch $branch2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminRole = Role::create(['name' => 'admin']);
        $this->cashierRole = Role::create(['name' => 'cashier']);

        $this->branch1 = Branch::create([
            'name' => 'Jakarta Branch',
            'code' => 'JKT-01',
            'address' => 'Jakarta',
            'phone' => '1234',
        ]);

        $this->branch2 = Branch::create([
            'name' => 'Bandung Branch',
            'code' => 'BDG-01',
            'address' => 'Bandung',
            'phone' => '5678',
        ]);
    }

    private function getSuperAdmin()
    {
        return User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@test.com',
            'password' => bcrypt('password'),
            'role_id' => $this->adminRole->id,
            'branch_id' => null,
            'is_active' => true,
        ]);
    }

    private function getBranchAdmin(Branch $branch, string $email = 'admin@branch.com', string $name = 'Branch Admin')
    {
        return User::create([
            'name' => $name,
            'email' => $email,
            'password' => bcrypt('password'),
            'role_id' => $this->adminRole->id,
            'branch_id' => $branch->id,
            'is_active' => true,
        ]);
    }

    private function getCashier(Branch $branch)
    {
        return User::create([
            'name' => 'Cashier User',
            'email' => 'cashier@branch.com',
            'password' => bcrypt('password'),
            'role_id' => $this->cashierRole->id,
            'branch_id' => $branch->id,
            'is_active' => true,
        ]);
    }

    public function test_guest_cannot_access_users_management(): void
    {
        $this->get(route('admin.users.index'))->assertRedirect(route('login'));
        $this->get(route('admin.users.create'))->assertRedirect(route('login'));
    }

    public function test_cashier_cannot_access_users_management(): void
    {
        $cashier = $this->getCashier($this->branch1);

        $this->actingAs($cashier)->get(route('admin.users.index'))->assertStatus(403);
        $this->actingAs($cashier)->get(route('admin.users.create'))->assertStatus(403);
    }

    public function test_branch_admin_can_only_see_users_in_their_own_branch(): void
    {
        $admin1 = $this->getBranchAdmin($this->branch1, 'admin1@test.com', 'Admin Jakarta');
        $cashier1 = $this->getCashier($this->branch1);

        $admin2 = $this->getBranchAdmin($this->branch2, 'admin2@test.com', 'Admin Bandung');

        // Set staff branch context for admin1 request
        $response = $this->actingAs($admin1)
            ->withSession(['branch_id' => $this->branch1->id])
            ->get(route('admin.users.index'));

        $response->assertStatus(200);
        $response->assertSee($cashier1->name);
        $response->assertDontSee($admin2->name);
    }

    public function test_super_admin_can_see_all_users_across_branches(): void
    {
        $super = $this->getSuperAdmin();
        $admin1 = $this->getBranchAdmin($this->branch1, 'admin1@test.com');
        $admin2 = $this->getBranchAdmin($this->branch2, 'admin2@test.com');

        $response = $this->actingAs($super)->get(route('admin.users.index'));

        $response->assertStatus(200);
        $response->assertSee($admin1->name);
        $response->assertSee($admin2->name);
    }

    public function test_branch_admin_creating_user_is_locked_to_their_branch(): void
    {
        $admin = $this->getBranchAdmin($this->branch1);

        $response = $this->actingAs($admin)
            ->withSession(['branch_id' => $this->branch1->id])
            ->post(route('admin.users.store'), [
                'name' => 'New Cashier',
                'email' => 'newcashier@test.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'role_id' => $this->cashierRole->id,
                'branch_id' => $this->branch2->id, // Attempt to assign to branch 2
            ]);

        $response->assertRedirect(route('admin.users.index'));

        // The branch_id should be forced to branch1 (the admin's branch)
        $this->assertDatabaseHas('users', [
            'email' => 'newcashier@test.com',
            'branch_id' => $this->branch1->id,
        ]);
    }

    public function test_super_admin_can_assign_user_to_any_branch(): void
    {
        $super = $this->getSuperAdmin();

        $response = $this->actingAs($super)->post(route('admin.users.store'), [
            'name' => 'Assigned Cashier',
            'email' => 'assigned@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role_id' => $this->cashierRole->id,
            'branch_id' => $this->branch2->id,
        ]);

        $response->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'assigned@test.com',
            'branch_id' => $this->branch2->id,
        ]);
    }

    public function test_branch_admin_cannot_edit_user_of_another_branch(): void
    {
        $admin = $this->getBranchAdmin($this->branch1, 'admin1@test.com');
        $otherUser = $this->getBranchAdmin($this->branch2, 'admin2@test.com');

        // ScopedToBranch global scope will make it return 404 or UserController will abort 403.
        // Let's assert a non-200/non-redirect status code
        $response = $this->actingAs($admin)
            ->withSession(['branch_id' => $this->branch1->id])
            ->get(route('admin.users.edit', $otherUser->id));

        $this->assertTrue(in_array($response->status(), [403, 404]));
    }

    public function test_user_cannot_delete_themselves(): void
    {
        $admin = $this->getBranchAdmin($this->branch1);

        $response = $this->actingAs($admin)->delete(route('admin.users.destroy', $admin->id));

        $response->assertRedirect();
        // User should still exist in database
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_user_cannot_suspend_themselves(): void
    {
        $admin = $this->getBranchAdmin($this->branch1);

        $response = $this->actingAs($admin)->patch(route('admin.users.toggle-active', $admin->id));

        $response->assertRedirect();

        $admin->refresh();
        $this->assertTrue($admin->is_active);
    }

    public function test_suspended_user_cannot_login(): void
    {
        $user = $this->getBranchAdmin($this->branch1);
        $user->is_active = false;
        $user->save();

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertFalse(auth()->check());
    }

    public function test_active_user_who_gets_suspended_is_logged_out_on_next_request(): void
    {
        $user = $this->getBranchAdmin($this->branch1);

        $this->actingAs($user);
        $this->assertTrue(auth()->check());

        // Suspend the user directly in database
        $user->is_active = false;
        $user->save();

        // Send a request to dashboard which is protected by branch.staff middleware (which uses RoleMiddleware or SetStaffBranchContext)
        // Wait, RoleMiddleware checks is_active. Let's make a request to a route protected by 'role:admin' or 'branch.staff' middleware
        $response = $this->get(route('dashboard'));

        // Should abort with 403 or redirect to login (since middleware logs them out)
        $this->assertTrue(in_array($response->status(), [302, 403]));
        $this->assertFalse(auth()->check());
    }
}
