<?php

namespace Tests\Feature;

use App\Models\AIImageSearchLog;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Menu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AiImageRecognitionTest extends TestCase
{
    use RefreshDatabase;

    private Branch $branch;

    private Category $category;

    private Menu $menu;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a branch
        $this->branch = Branch::create([
            'name' => 'Krave Scan Jakarta',
            'code' => 'JKT-01',
            'address' => 'Sudirman Jakarta',
            'phone' => '021-123456',
        ]);

        // Create category and a menu item
        $this->category = Category::create(['name' => 'Food']);
        $this->menu = Menu::create([
            'category_id' => $this->category->id,
            'name' => 'Nasi Goreng Kampung',
            'description' => 'Nasi goreng khas nusantara.',
            'price' => 35000.00,
            'is_active' => true,
        ]);
    }

    /**
     * Test validation fails when no image is uploaded.
     */
    public function test_validation_fails_without_image(): void
    {
        $response = $this->postJson(route('customer.menu.identify', [
            'branch_code' => $this->branch->code,
        ]), []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['image']);
    }

    /**
     * Test validation fails when the uploaded file is not an image.
     */
    public function test_validation_fails_with_invalid_file_type(): void
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->create('document.pdf', 500, 'application/pdf');

        $response = $this->postJson(route('customer.menu.identify', [
            'branch_code' => $this->branch->code,
        ]), [
            'image' => $file,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['image']);
    }

    /**
     * Test successful menu identification via mocked AI service.
     */
    public function test_successful_menu_identification(): void
    {
        Storage::fake('public');
        Http::fake([
            '*/predict' => Http::response([
                'success' => true,
                'prediction' => 'Nasi Goreng',
                'confidence' => 0.95,
                'method' => 'mobilenet_v2_fried_rice',
            ], 200),
        ]);

        $file = UploadedFile::fake()->image('nasi_goreng.jpg');

        $response = $this->postJson(route('customer.menu.identify', [
            'branch_code' => $this->branch->code,
        ]), [
            'image' => $file,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'menu_id' => $this->menu->id,
            'menu_name' => $this->menu->name,
            'confidence' => 0.95,
        ]);

        // Assert log was created in DB
        $this->assertDatabaseHas('a_i_image_search_logs', [
            'matched_menu_id' => $this->menu->id,
            'confidence_score' => 0.95,
        ]);

        // Assert file was saved on disk
        $log = AIImageSearchLog::first();
        Storage::disk('public')->assertExists($log->image_path);
    }

    /**
     * Test scenario when the AI service returns a prediction but it is not found in KraveScan.
     */
    public function test_menu_not_found_in_database(): void
    {
        Storage::fake('public');
        Http::fake([
            '*/predict' => Http::response([
                'success' => true,
                'prediction' => 'Pizza Rendang',
                'confidence' => 0.88,
                'method' => 'mobilenet_v2_pizza',
            ], 200),
        ]);

        $file = UploadedFile::fake()->image('pizza.jpg');

        $response = $this->postJson(route('customer.menu.identify', [
            'branch_code' => $this->branch->code,
        ]), [
            'image' => $file,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => false,
            'message' => 'Menu tidak dikenali atau tidak aktif di cabang ini.',
        ]);

        // Assert log has matched_menu_id = null
        $this->assertDatabaseHas('a_i_image_search_logs', [
            'matched_menu_id' => null,
            'confidence_score' => 0.88,
        ]);
    }

    /**
     * Test scenario when the AI service fails or times out.
     */
    public function test_ai_service_failure_handling(): void
    {
        Storage::fake('public');
        Http::fake([
            '*/predict' => function () {
                throw new ConnectionException('Connection timed out');
            },
        ]);

        $file = UploadedFile::fake()->image('katsu.jpg');

        $response = $this->postJson(route('customer.menu.identify', [
            'branch_code' => $this->branch->code,
        ]), [
            'image' => $file,
        ]);

        // 503 Service Unavailable
        $response->assertStatus(503);
        $response->assertJson([
            'success' => false,
            'message' => 'Layanan AI sedang tidak tersedia. Silakan gunakan pencarian manual.',
        ]);

        // Check that attempt was logged with null score/menu
        $this->assertDatabaseHas('a_i_image_search_logs', [
            'matched_menu_id' => null,
            'confidence_score' => null,
        ]);
    }
}
