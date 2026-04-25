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
        // 1. Rename infospot_products to products (Already done)
        // Schema::rename('infospot_products', 'products');

        // 2. Rename infospot_assets to product_assets (Already done)
        // Schema::rename('infospot_assets', 'product_assets');

        // 3. Update columns in product_assets
        Schema::table('product_assets', function (Blueprint $table) {
            // Rename infospot_product_id to product_id (Already done)
            // $table->renameColumn('infospot_product_id', 'product_id');
            
            // Drop infospot_id as assets now belong to products
            // Note: The foreign key name remains infospot_assets_... because it was created on that table
            $table->dropForeign('infospot_assets_infospot_id_foreign');
            $table->dropColumn('infospot_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_assets', function (Blueprint $table) {
            $table->unsignedBigInteger('infospot_id')->nullable();
            $table->foreign('infospot_id')->references('id')->on('infospots')->onDelete('cascade');
            $table->renameColumn('product_id', 'infospot_product_id');
        });
        Schema::rename('product_assets', 'infospot_assets');
        Schema::rename('products', 'infospot_products');
    }
};
