<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test database seeding.
     */
    public function test_database_can_be_seeded(): void
    {
        // Seed the database
        $this->seed();

        // Check if role data is created
        $this->assertDatabaseHas('roles', [
            'name' => 'admin',
        ]);
        $this->assertDatabaseHas('roles', [
            'name' => 'cashier',
        ]);

        // Check if branch data is created
        $this->assertDatabaseHas('branches', [
            'code' => 'JKT-01',
        ]);
        $this->assertDatabaseHas('branches', [
            'code' => 'BDG-01',
        ]);

        // Check if user data is created
        $this->assertDatabaseHas('users', [
            'email' => 'superadmin@kravescan.com',
        ]);

        // Check if categories are seeded
        $this->assertDatabaseHas('categories', [
            'name' => 'Coffee',
        ]);

        // Check if menu is seeded
        $this->assertDatabaseHas('menus', [
            'name' => 'Espresso',
        ]);
    }
}
