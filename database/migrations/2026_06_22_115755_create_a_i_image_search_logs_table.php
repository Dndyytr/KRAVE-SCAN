<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('a_i_image_search_logs', function (Blueprint $table) {
            $table->id();
            $table->string('image_path');
            $table->foreignId('matched_menu_id')->nullable()->constrained('menus')->nullOnDelete();
            $table->decimal('confidence_score', 5, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('a_i_image_search_logs');
    }
};
