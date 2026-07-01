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

    private Role $kitchenRole;

    private Branch $branch1;

    private Branch $branch2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminRole = Role::create(['name' => 'admin']);
        $this->cashierRole = Role::create(['name' => 'cashier']);
        $this->kitchenRole = Role::create(['name' => 'kitchen']);

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

    private function getKitchenUser(Branch $branch)
    {
        return User::create([
            'name' => 'Kitchen User',
            'email' => 'kitchen_'.$branch->code.'@test.com',
            'password' => bcrypt('password'),
            'role_id' => $this->kitchenRole->id,
            'branch_id' => $branch->id,
        ]);
    }

    public function test_guest_cannot_access_categories_crud(): void
    {
        $this->get(route('cashier.categories.index'))->assertRedirect(route('login'));
        $this->get(route('cashier.categories.create'))->assertRedirect(route('login'));
        $this->post(route('cashier.categories.store'), [])->assertRedirect(route('login'));
    }

    public function test_kitchen_cannot_access_categories_crud(): void
    {
        $kitchen = $this->getKitchenUser($this->branch1);

        $this->actingAs($kitchen)->get(route('cashier.categories.index'))->assertStatus(403);
        $this->actingAs($kitchen)->get(route('cashier.categories.create'))->assertStatus(403);
        $this->actingAs($kitchen)->post(route('cashier.categories.store'), [])->assertStatus(403);
    }

    public function test_cashier_can_access_categories_index(): void
    {
        $cashier = $this->getCashierUser($this->branch1);

        $category = Category::create([
            'name' => 'Coffee BR1',
            'branch_id' => $this->branch1->id,
        ]);

        $response = $this->actingAs($cashier)->get(route('cashier.categories.index'));

        $response->assertStatus(200);
        $response->assertSee('Coffee BR1');
    }

    public function test_cashier_can_create_category(): void
    {
        $cashier = $this->getCashierUser($this->branch1);

        $response = $this->actingAs($cashier)->post(route('cashier.categories.store'), [
            'name' => 'Ice Blended',
        ]);

        $response->assertRedirect(route('cashier.categories.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('categories', [
            'name' => 'Ice Blended',
            'branch_id' => $this->branch1->id,
        ]);
    }

    public function test_cashier_cannot_create_duplicate_category_in_same_branch(): void
    {
        $cashier = $this->getCashierUser($this->branch1);

        Category::create([
            'name' => 'Snacks',
            'branch_id' => $this->branch1->id,
        ]);

        $response = $this->actingAs($cashier)->post(route('cashier.categories.store'), [
            'name' => 'Snacks',
        ]);

        $response->assertSessionHasErrors('name');
        $this->assertEquals(1, Category::where('name', 'Snacks')->where('branch_id', $this->branch1->id)->count());
    }

    public function test_cashier_can_create_same_category_name_in_different_branches(): void
    {
        Category::create([
            'name' => 'Signature',
            'branch_id' => $this->branch1->id,
        ]);

        $cashier2 = $this->getCashierUser($this->branch2);

        $response = $this->actingAs($cashier2)->post(route('cashier.categories.store'), [
            'name' => 'Signature',
        ]);

        $response->assertRedirect(route('cashier.categories.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('categories', [
            'name' => 'Signature',
            'branch_id' => $this->branch2->id,
        ]);
    }

    public function test_cashier_can_update_category(): void
    {
        $cashier = $this->getCashierUser($this->branch1);

        $category = Category::create([
            'name' => 'Main Course Old',
            'branch_id' => $this->branch1->id,
        ]);

        $response = $this->actingAs($cashier)->put(route('cashier.categories.update', $category->id), [
            'name' => 'Main Course New',
        ]);

        $response->assertRedirect(route('cashier.categories.index'));
        $category->refresh();
        $this->assertEquals('Main Course New', $category->name);
    }

    public function test_cashier_can_delete_category_without_menus(): void
    {
        $cashier = $this->getCashierUser($this->branch1);

        $category = Category::create([
            'name' => 'Dessert',
            'branch_id' => $this->branch1->id,
        ]);

        $response = $this->actingAs($cashier)->delete(route('cashier.categories.destroy', $category->id));

        $response->assertRedirect(route('cashier.categories.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_cashier_cannot_delete_category_with_menus(): void
    {
        $cashier = $this->getCashierUser($this->branch1);

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

        $response = $this->actingAs($cashier)->delete(route('cashier.categories.destroy', $category->id));

        $response->assertRedirect(route('cashier.categories.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_branch_isolation_for_categories(): void
    {
        $cashier1 = $this->getCashierUser($this->branch1);

        $category2 = Category::create([
            'name' => 'Branch 2 Exclusive',
            'branch_id' => $this->branch2->id,
        ]);

        $response = $this->actingAs($cashier1)->get(route('cashier.categories.index'));
        $response->assertDontSee('Branch 2 Exclusive');

        $response = $this->actingAs($cashier1)->get(route('cashier.categories.edit', $category2->id));
        $response->assertStatus(404);

        $response = $this->actingAs($cashier1)->put(route('cashier.categories.update', $category2->id), [
            'name' => 'Hacked Name',
        ]);
        $response->assertStatus(404);

        $response = $this->actingAs($cashier1)->delete(route('cashier.categories.destroy', $category2->id));
        $response->assertStatus(404);
    }
}
