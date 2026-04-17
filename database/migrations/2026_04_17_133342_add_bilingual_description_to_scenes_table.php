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
        Schema::table('scenes', function (Blueprint $table) {
            $table->text('description_id')->nullable()->after('name');
            $table->text('description_en')->nullable()->after('description_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scenes', function (Blueprint $table) {
            $table->dropColumn(['description_id', 'description_en']);
        });
    }
};
