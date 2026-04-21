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
        Schema::create('scene_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scene_id')->constrained()->cascadeOnDelete();
            $table->string('name')->default('Main View');
            $table->string('image_path');
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });

        // Move existing data
        $scenes = DB::table('scenes')->get();
        foreach ($scenes as $scene) {
            DB::table('scene_views')->insert([
                'scene_id' => $scene->id,
                'name' => 'Main View',
                'image_path' => $scene->image_path,
                'is_primary' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Remove old column
        Schema::table('scenes', function (Blueprint $table) {
            $table->dropColumn('image_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scene_views');
    }
};
