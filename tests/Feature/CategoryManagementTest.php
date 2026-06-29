<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryManagementTest extends TestCase
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
            'name' => 'Branch One',
            'code' => 'BR-01',
            'address' => 'Address One',
            'phone' => '1111111111',
        ]);

        $this->branch2 = Branch::create([
            'name' => 'Branch Two',
            'code' => 'BR-02',
            'address' => 'Address Two',
            'phone' => '2222222222',
        ]);
    }

    private function getAdminUser(Branch $branch)
    {
        return User::create([
            'name' => 'Admin User',
            'email' => 'admin_'.$branch->code.'@test.com',
            'password' => bcrypt('password'),
            'role_id' => $this->adminRole->id,
            'branch_id' => $branch->id,
        ]);
    }

    private function getCashierUser(Branch $branch)
    {
        return User::create([
            'name' => 'Cashier User',
            'email' => 'cashier_'.$branch->code.'@test.com',
            'password' => bcrypt('password'),
            'role_id' => $this->cashierRole->id,
            'branch_id' => $branch->id,
        ]);
    }

    public function test_guest_cannot_access_categories_crud(): void
    {
        $this->get(route('admin.categories.index'))->assertRedirect(route('login'));
        $this->get(route('admin.categories.create'))->assertRedirect(route('login'));
        $this->post(route('admin.categories.store'), [])->assertRedirect(route('login'));
    }

    public function test_cashier_cannot_access_categories_crud(): void
    {
        $cashier = $this->getCashierUser($this->branch1);

        $this->actingAs($cashier)->get(route('admin.categories.index'))->assertStatus(403);
        $this->actingAs($cashier)->get(route('admin.categories.create'))->assertStatus(403);
        $this->actingAs($cashier)->post(route('admin.categories.store'), [])->assertStatus(403);
    }

    public function test_admin_can_access_categories_index(): void
    {
        $admin = $this->getAdminUser($this->branch1);

        // Use actingAs and branch session context if set by middleware, or rely on model ScopedToBranch
        // Note: SetStaffBranchContext will run upon request
        $category = Category::create([
            'name' => 'Coffee BR1',
            'branch_id' => $this->branch1->id,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.categories.index'));

        $response->assertStatus(200);
        $response->assertSee('Coffee BR1');
    }

    public function test_admin_can_create_category(): void
    {
        $admin = $this->getAdminUser($this->branch1);

        $response = $this->actingAs($admin)->post(route('admin.categories.store'), [
            'name' => 'Ice Blended',
        ]);

        $response->assertRedirect(route('admin.categories.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('categories', [
            'name' => 'Ice Blended',
            'branch_id' => $this->branch1->id,
        ]);
    }

    public function test_admin_cannot_create_duplicate_category_in_same_branch(): void
    {
        $admin = $this->getAdminUser($this->branch1);

        Category::create([
            'name' => 'Snacks',
            'branch_id' => $this->branch1->id,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.categories.store'), [
            'name' => 'Snacks',
        ]);

        $response->assertSessionHasErrors('name');
        // Check only one Category exists in DB for branch1
        $this->assertEquals(1, Category::where('name', 'Snacks')->where('branch_id', $this->branch1->id)->count());
    }

    public function test_admin_can_create_same_category_name_in_different_branches(): void
    {
        // First branch category
        Category::create([
            'name' => 'Signature',
            'branch_id' => $this->branch1->id,
        ]);

        // Create with Admin Branch Two
        $admin2 = $this->getAdminUser($this->branch2);

        $response = $this->actingAs($admin2)->post(route('admin.categories.store'), [
            'name' => 'Signature',
        ]);

        $response->assertRedirect(route('admin.categories.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('categories', [
            'name' => 'Signature',
            'branch_id' => $this->branch2->id,
        ]);
    }

    public function test_admin_can_update_category(): void
    {
        $admin = $this->getAdminUser($this->branch1);

        $category = Category::create([
            'name' => 'Main Course Old',
            'branch_id' => $this->branch1->id,
        ]);

        $response = $this->actingAs($admin)->put(route('admin.categories.update', $category->id), [
            'name' => 'Main Course New',
        ]);

        $response->assertRedirect(route('admin.categories.index'));
        $category->refresh();
        $this->assertEquals('Main Course New', $category->name);
    }

    public function test_admin_can_delete_category_without_menus(): void
    {
        $admin = $this->getAdminUser($this->branch1);

        $category = Category::create([
            'name' => 'Dessert',
            'branch_id' => $this->branch1->id,
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.categories.destroy', $category->id));

        $response->assertRedirect(route('admin.categories.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_admin_cannot_delete_category_with_menus(): void
    {
        $admin = $this->getAdminUser($this->branch1);

        $category = Category::create([
            'name' => 'Drinks',
            'branch_id' => $this->branch1->id,
        ]);

        Menu::create([
            'name' => 'Lemon Tea',
            'category_id' => $category->id,
            'price' => 12000,
            'is_active' => true,
            'branch_id' => $this->branch1->id,
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.categories.destroy', $category->id));

        $response->assertRedirect(route('admin.categories.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_branch_isolation_for_categories(): void
    {
        // Admin of Branch 1 should not see/edit/delete Branch 2's categories
        $admin1 = $this->getAdminUser($this->branch1);

        // Category of Branch 2
        $category2 = Category::create([
            'name' => 'Branch 2 Exclusive',
            'branch_id' => $this->branch2->id,
        ]);

        // Try to access index
        $response = $this->actingAs($admin1)->get(route('admin.categories.index'));
        $response->assertDontSee('Branch 2 Exclusive');

        // Try to edit/update category2 (should abort 404 because global scope restricts to branch1)
        $response = $this->actingAs($admin1)->get(route('admin.categories.edit', $category2->id));
        $response->assertStatus(404);

        $response = $this->actingAs($admin1)->put(route('admin.categories.update', $category2->id), [
            'name' => 'Hacked Name',
        ]);
        $response->assertStatus(404);

        $response = $this->actingAs($admin1)->delete(route('admin.categories.destroy', $category2->id));
        $response->assertStatus(404);
    }
}
