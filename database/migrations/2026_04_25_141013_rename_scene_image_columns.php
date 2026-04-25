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
            $table->renameColumn('image_path', 'high_res_path');
            $table->renameColumn('thumbnail_path', 'low_res_path');
            $table->renameColumn('sidebar_path', 'thumbnail_path');
            $table->renameColumn('medium_path', 'medium_res_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scenes', function (Blueprint $table) {
            //
        });
    }
};
