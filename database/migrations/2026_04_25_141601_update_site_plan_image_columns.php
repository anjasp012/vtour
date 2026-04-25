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
        if (Schema::hasColumn('site_plans', 'image_path')) {
            Schema::table('site_plans', function (Blueprint $table) {
                $table->renameColumn('image_path', 'high_res_path');
            });
        }

        Schema::table('site_plans', function (Blueprint $table) {
            if (!Schema::hasColumn('site_plans', 'thumbnail_path')) {
                $table->string('thumbnail_path')->nullable()->after('high_res_path');
            }
            if (!Schema::hasColumn('site_plans', 'low_res_path')) {
                $table->string('low_res_path')->nullable()->after('thumbnail_path');
            }
            if (!Schema::hasColumn('site_plans', 'medium_res_path')) {
                $table->string('medium_res_path')->nullable()->after('low_res_path');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_plans', function (Blueprint $table) {
            //
        });
    }
};
