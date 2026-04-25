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
        // 1. Remove tour_id from scenes
        Schema::table('scenes', function (Blueprint $table) {
            $table->dropForeign(['tour_id']);
            $table->dropColumn('tour_id');
        });

        // 2. Remove tour_id from site_plans
        Schema::table('site_plans', function (Blueprint $table) {
            $table->dropForeign(['tour_id']);
            $table->dropColumn('tour_id');
        });

        // 3. Drop tours table
        Schema::dropIfExists('tours');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This is a destructive simplification as requested, rollback would be complex
        // but for safety, we define the structure back if needed (empty for now or recreate tours)
    }
};
