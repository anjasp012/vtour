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
        Schema::create('site_plan_hotspots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_plan_id')->constrained()->onDelete('cascade');
            $table->foreignId('scene_id')->constrained()->onDelete('cascade');
            $table->decimal('x', 8, 4);
            $table->decimal('y', 8, 4);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_plan_hotspots');
    }
};
