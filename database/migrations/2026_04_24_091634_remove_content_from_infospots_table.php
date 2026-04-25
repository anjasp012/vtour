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
        Schema::table('infospots', function (Blueprint $table) {
            $table->dropColumn(['content_id', 'content_en']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('infospots', function (Blueprint $table) {
            $table->text('content_id')->nullable();
            $table->text('content_en')->nullable();
        });
    }
};
