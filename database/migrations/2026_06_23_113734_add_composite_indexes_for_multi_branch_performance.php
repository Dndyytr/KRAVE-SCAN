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
        Schema::table('orders', function (Blueprint $table) {
            $table->index(['branch_id', 'status']);
            $table->index(['branch_id', 'created_at']);
        });

        Schema::table('automation_logs', function (Blueprint $table) {
            $table->index(['branch_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['branch_id', 'status']);
            $table->dropIndex(['branch_id', 'created_at']);
        });

        Schema::table('automation_logs', function (Blueprint $table) {
            $table->dropIndex(['branch_id', 'status']);
        });
    }
};
