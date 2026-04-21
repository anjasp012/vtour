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
        Schema::create('infospot_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('infospot_id')->constrained()->cascadeOnDelete();
            $table->string('file_type'); // '3d' or '2d'
            $table->string('file_path');
            $table->string('label')->nullable(); // optional caption/label
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('infospot_assets');
    }
};
