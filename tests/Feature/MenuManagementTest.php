<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MenuManagementTest extends TestCase
{
    use RefreshDatabase;

    private Role $adminRole;

    private Role $cashierRole;

    private Role $kitchenRole;

    private Branch $branch;

    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminRole = Role::create(['name' => 'admin']);
        $this->cashierRole = Role::create(['name' => 'cashier']);
        $this->kitchenRole = Role::create(['name' => 'kitchen']);
        $this->branch = Branch::create([
            'name' => 'Test Branch',
            'code' => 'TEST-01',
            'address' => 'Test Address',
            'phone' => '1234567890',
        ]);
        $this->category = Category::create(['name' => 'Coffee']);
    }

    private function getAdminUser()
    {
        return User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role_id' => $this->adminRole->id,
            'branch_id' => $this->branch->id,
        ]);
    }

    private function getCashierUser()
    {
        return User::create([
            'name' => 'Cashier User',
            'email' => 'cashier@test.com',
            'password' => bcrypt('password'),
            'role_id' => $this->cashierRole->id,
            'branch_id' => $this->branch->id,
        ]);
    }

    private function getKitchenUser()
    {
        return User::create([
            'name' => 'Kitchen User',
            'email' => 'kitchen@test.com',
            'password' => bcrypt('password'),
            'role_id' => $this->kitchenRole->id,
            'branch_id' => $this->branch->id,
        ]);
    }

    public function test_guest_cannot_access_menus_crud(): void
    {
        $this->get(route('cashier.menus.index'))->assertRedirect(route('login'));
        $this->get(route('cashier.menus.create'))->assertRedirect(route('login'));
        $this->post(route('cashier.menus.store'), [])->assertRedirect(route('login'));
    }

    public function test_kitchen_cannot_access_menus_crud(): void
    {
        $kitchen = $this->getKitchenUser();

        $this->actingAs($kitchen)->get(route('cashier.menus.index'))->assertStatus(403);
        $this->actingAs($kitchen)->get(route('cashier.menus.create'))->assertStatus(403);
        $this->actingAs($kitchen)->post(route('cashier.menus.store'), [])->assertStatus(403);
    }

    public function test_cashier_can_access_menus_index(): void
    {
        $cashier = $this->getCashierUser();
        $menu = Menu::create([
            'name' => 'Espresso Classic Test',
            'category_id' => $this->category->id,
            'price' => 15000,
            'is_active' => true,
        ]);

        $response = $this->actingAs($cashier)->get(route('cashier.menus.index'));

        $response->assertStatus(200);
        $response->assertSee('Espresso Classic Test');
    }

    public function test_cashier_can_create_menu_with_image(): void
    {
        Storage::fake('public');
        $cashier = $this->getCashierUser();

        $image = UploadedFile::fake()->image('espresso.jpg');

        $response = $this->actingAs($cashier)->post(route('cashier.menus.store'), [
            'name' => 'Espresso Classic',
            'category_id' => $this->category->id,
            'price' => 15000,
            'description' => 'A classic double espresso shot',
            'is_active' => 1,
            'image' => $image,
        ]);

        $response->assertRedirect(route('cashier.menus.index'));

        $this->assertDatabaseHas('menus', [
            'name' => 'Espresso Classic',
            'category_id' => $this->category->id,
            'price' => 15000,
            'is_active' => true,
        ]);

        $menu = Menu::where('name', 'Espresso Classic')->first();
        $this->assertNotNull($menu->image_path);

        $relativeOldPath = str_replace('storage/', '', $menu->image_path);
        Storage::disk('public')->assertExists($relativeOldPath);
    }

    public function test_cashier_can_update_menu_and_image(): void
    {
        Storage::fake('public');
        $cashier = $this->getCashierUser();

        $menu = Menu::create([
            'name' => 'Old Name',
            'category_id' => $this->category->id,
            'price' => 10000,
            'is_active' => false,
            'image_path' => 'storage/menus/old.jpg',
        ]);

        // Place old image in fake storage
        Storage::disk('public')->put('menus/old.jpg', 'fake content');

        $newImage = UploadedFile::fake()->image('new.jpg');

        $response = $this->actingAs($cashier)->put(route('cashier.menus.update', $menu->id), [
            'name' => 'New Name',
            'category_id' => $this->category->id,
            'price' => 12000,
            'description' => 'New description',
            'is_active' => 1,
            'image' => $newImage,
        ]);

        $response->assertRedirect(route('cashier.menus.index'));

        $menu->refresh();
        $this->assertEquals('New Name', $menu->name);
        $this->assertEquals(12000, (int) $menu->price);
        $this->assertTrue($menu->is_active);

        // Assert old file deleted and new file exists
        Storage::disk('public')->assertMissing('menus/old.jpg');
        $newRelativePath = str_replace('storage/', '', $menu->image_path);
        Storage::disk('public')->assertExists($newRelativePath);
    }

    public function test_cashier_can_delete_menu(): void
    {
        Storage::fake('public');
        $cashier = $this->getCashierUser();

        $menu = Menu::create([
            'name' => 'Espresso Classic',
            'category_id' => $this->category->id,
            'price' => 15000,
            'is_active' => true,
            'image_path' => 'storage/menus/espresso.jpg',
        ]);

        Storage::disk('public')->put('menus/espresso.jpg', 'fake content');

        $response = $this->actingAs($cashier)->delete(route('cashier.menus.destroy', $menu->id));

        $response->assertRedirect(route('cashier.menus.index'));
        $this->assertDatabaseMissing('menus', ['id' => $menu->id]);
        Storage::disk('public')->assertMissing('menus/espresso.jpg');
    }

    public function test_cashier_can_toggle_menu_active_status(): void
    {
        $cashier = $this->getCashierUser();
        $menu = Menu::create([
            'name' => 'Espresso Classic',
            'category_id' => $this->category->id,
            'price' => 15000,
            'is_active' => true,
        ]);

        // Toggle to false
        $response = $this->actingAs($cashier)->patchJson(route('cashier.menus.toggle-active', $menu->id));
        $response->assertJson([
            'success' => true,
            'is_active' => false,
        ]);

        $menu->refresh();
        $this->assertFalse($menu->is_active);

        // Toggle back to true
        $response = $this->actingAs($cashier)->patchJson(route('cashier.menus.toggle-active', $menu->id));
        $response->assertJson([
            'success' => true,
            'is_active' => true,
        ]);

        $menu->refresh();
        $this->assertTrue($menu->is_active);
    }

    public function test_inactive_menu_is_not_visible_to_customers(): void
    {
        // Active menu
        $activeMenu = Menu::create([
            'name' => 'Active Coffee Test',
            'category_id' => $this->category->id,
            'price' => 15000,
            'is_active' => true,
        ]);

        // Inactive menu
        $inactiveMenu = Menu::create([
            'name' => 'Inactive Food Test',
            'category_id' => $this->category->id,
            'price' => 20000,
            'is_active' => false,
        ]);

        // Customers can access the menu listing (no auth required)
        $response = $this->get(route('customer.menu', [
            'branch_code' => $this->branch->code,
            'table_number' => 1,
        ]));

        $response->assertStatus(200);
        $response->assertSee('Active Coffee Test');
        $response->assertDontSee('Inactive Food Test');
    }
}
