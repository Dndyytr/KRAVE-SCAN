<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Role;
use App\Models\User;
use App\Services\BranchContext;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ActivityLoggingTest extends TestCase
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

    private function getBranchAdmin(Branch $branch)
    {
        return User::create([
            'name' => 'Branch Admin '.$branch->name,
            'email' => 'admin@'.$branch->code.'.com',
            'password' => bcrypt('password'),
            'role_id' => $this->adminRole->id,
            'branch_id' => $branch->id,
            'is_active' => true,
        ]);
    }

    private function getCashier(Branch $branch)
    {
        return User::create([
            'name' => 'Cashier '.$branch->name,
            'email' => 'cashier@'.$branch->code.'.com',
            'password' => bcrypt('password'),
            'role_id' => $this->cashierRole->id,
            'branch_id' => $branch->id,
            'is_active' => true,
        ]);
    }

    public function test_guest_cannot_access_activity_logs(): void
    {
        $this->get(route('admin.activity-logs.index'))->assertRedirect(route('login'));
    }

    public function test_cashier_cannot_access_activity_logs(): void
    {
        $cashier = $this->getCashier($this->branch1);

        $this->actingAs($cashier)
            ->get(route('admin.activity-logs.index'))
            ->assertStatus(403);
    }

    public function test_branch_admin_can_only_see_activity_logs_of_their_own_branch(): void
    {
        $admin1 = $this->getBranchAdmin($this->branch1);
        $admin2 = $this->getBranchAdmin($this->branch2);

        // Directly insert logs for branch 1 and branch 2
        ActivityLog::create([
            'user_id' => $admin1->id,
            'branch_id' => $this->branch1->id,
            'action' => 'updated',
            'ip_address' => '127.0.0.1',
        ]);

        ActivityLog::create([
            'user_id' => $admin2->id,
            'branch_id' => $this->branch2->id,
            'action' => 'created',
            'ip_address' => '192.168.1.1',
        ]);

        // Branch Admin 1 accesses the logs with branch session
        $response = $this->actingAs($admin1)
            ->withSession(['branch_id' => $this->branch1->id])
            ->get(route('admin.activity-logs.index'));

        $response->assertStatus(200);
        $response->assertSee('127.0.0.1');
        $response->assertDontSee('192.168.1.1');
    }

    public function test_super_admin_can_see_all_activity_logs(): void
    {
        $superAdmin = $this->getSuperAdmin();
        $admin1 = $this->getBranchAdmin($this->branch1);
        $admin2 = $this->getBranchAdmin($this->branch2);

        ActivityLog::create([
            'user_id' => $admin1->id,
            'branch_id' => $this->branch1->id,
            'action' => 'updated',
            'ip_address' => '127.0.0.1',
        ]);

        ActivityLog::create([
            'user_id' => $admin2->id,
            'branch_id' => $this->branch2->id,
            'action' => 'created',
            'ip_address' => '192.168.1.1',
        ]);

        // Super Admin accesses the logs (without any active branch filtering)
        $response = $this->actingAs($superAdmin)
            ->get(route('admin.activity-logs.index'));

        $response->assertStatus(200);
        $response->assertSee('127.0.0.1');
        $response->assertSee('192.168.1.1');
    }

    public function test_auto_logging_on_menu_events(): void
    {
        $admin = $this->getBranchAdmin($this->branch1);
        $this->actingAs($admin);

        // In this test, we run within branch context.
        // We set active branch in session or simulate the middleware's BranchContext binding.
        app(BranchContext::class)->setBranch($this->branch1);

        $category = Category::create([
            'name' => 'Minuman',
            'branch_id' => $this->branch1->id,
        ]);

        // 1. Create event
        $menu = Menu::create([
            'branch_id' => $this->branch1->id,
            'category_id' => $category->id,
            'name' => 'Es Teh Manis',
            'price' => 5000,
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'created',
            'loggable_type' => Menu::class,
            'loggable_id' => $menu->id,
            'user_id' => $admin->id,
            'branch_id' => $this->branch1->id,
        ]);

        // 2. Update event
        $menu->update([
            'price' => 6000,
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'updated',
            'loggable_type' => Menu::class,
            'loggable_id' => $menu->id,
            'user_id' => $admin->id,
        ]);

        // Inspect that old_values and new_values are correctly saved
        $updateLog = ActivityLog::where('action', 'updated')
            ->where('loggable_type', Menu::class)
            ->where('loggable_id', $menu->id)
            ->first();

        $this->assertNotNull($updateLog);
        $this->assertEquals(5000, $updateLog->old_values['price']);
        $this->assertEquals(6000, $updateLog->new_values['price']);

        // 3. Delete event
        $menuId = $menu->id;
        $menu->delete();

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'deleted',
            'loggable_type' => Menu::class,
            'loggable_id' => $menuId,
            'user_id' => $admin->id,
        ]);
    }

    public function test_login_and_logout_events_are_logged(): void
    {
        $admin = $this->getBranchAdmin($this->branch1);

        // Simulate Login
        $response = $this->post(route('login'), [
            'email' => $admin->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'login',
            'user_id' => $admin->id,
            'branch_id' => $this->branch1->id,
        ]);

        // Simulate Logout
        Auth::login($admin);
        $response = $this->post(route('logout'));
        $response->assertRedirect('/');

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'logout',
            'user_id' => $admin->id,
            'branch_id' => $this->branch1->id,
        ]);
    }

    public function test_clean_activity_logs_command(): void
    {
        $admin = $this->getBranchAdmin($this->branch1);

        // Create log older than 30 days
        $oldLog = ActivityLog::create([
            'user_id' => $admin->id,
            'branch_id' => $this->branch1->id,
            'action' => 'login',
        ]);
        // Manually update created_at via query builder to bypass timestamps updating
        ActivityLog::where('id', $oldLog->id)->update([
            'created_at' => Carbon::now()->subDays(31),
        ]);

        // Create log within 30 days
        $recentLog = ActivityLog::create([
            'user_id' => $admin->id,
            'branch_id' => $this->branch1->id,
            'action' => 'logout',
        ]);

        // Run the console command
        Artisan::call('activity-logs:clean', ['days' => 30]);

        // Assert old log is deleted, recent log still exists
        $this->assertDatabaseMissing('activity_logs', ['id' => $oldLog->id]);
        $this->assertDatabaseHas('activity_logs', ['id' => $recentLog->id]);
    }
}
