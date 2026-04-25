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
        Schema::table('infospot_assets', function (Blueprint $table) {
            $table->dropColumn(['description_id', 'description_en']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('infospot_assets', function (Blueprint $table) {
            $table->text('description_id')->nullable();
            $table->text('description_en')->nullable();
        });
    }
};
