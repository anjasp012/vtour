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
            $table->boolean('is_perspective')->default(false)->after('target_scene_id');
            $table->float('rotation_x')->default(0)->after('is_perspective');
            $table->float('rotation_y')->default(0)->after('rotation_x');
            $table->float('rotation_z')->default(0)->after('rotation_y');
            $table->float('scale_x')->default(1)->after('rotation_z');
            $table->float('scale_y')->default(1)->after('scale_x');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('infospots', function (Blueprint $table) {
            $table->dropColumn(['is_perspective', 'rotation_x', 'rotation_y', 'rotation_z', 'scale_x', 'scale_y']);
        });
    }
};
