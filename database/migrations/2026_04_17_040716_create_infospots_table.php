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
        Schema::create('infospots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scene_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // 'info' or 'nav'
            $table->integer('position_x');
            $table->integer('position_y');
            $table->integer('position_z');
            $table->string('title')->nullable();
            $table->text('content_id')->nullable();
            $table->text('content_en')->nullable();
            $table->string('model_path')->nullable();
            $table->foreignId('target_scene_id')->nullable()->constrained('scenes')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('infospots');
    }
};
