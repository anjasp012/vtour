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
        Schema::table('infospot_products', function (Blueprint $table) {
            $table->text('researcher')->nullable()->after('description_en');
            $table->text('contact_person')->nullable()->after('researcher');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('infospot_products', function (Blueprint $table) {
            //
        });
    }
};
