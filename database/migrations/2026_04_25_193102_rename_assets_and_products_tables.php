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
        // 1. Rename infospot_products to products
        if (Schema::hasTable('infospot_products')) {
            Schema::rename('infospot_products', 'products');
        }

        // 2. Rename infospot_assets to product_assets
        if (Schema::hasTable('infospot_assets')) {
            Schema::rename('infospot_assets', 'product_assets');
        }

        // 3. Update columns in product_assets
        Schema::table('product_assets', function (Blueprint $table) {
            // Rename infospot_product_id to product_id
            if (Schema::hasColumn('product_assets', 'infospot_product_id')) {
                $table->renameColumn('infospot_product_id', 'product_id');
            }
            
            // Drop infospot_id as assets now belong to products
            // Note: The foreign key name remains infospot_assets_... because it was created on that table
            if (Schema::hasColumn('product_assets', 'infospot_id')) {
                // Try to drop foreign key by name, then column
                try {
                    $table->dropForeign('infospot_assets_infospot_id_foreign');
                } catch (\Exception $e) {
                    // Fallback if name is different
                }
                $table->dropColumn('infospot_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('product_assets')) {
            Schema::table('product_assets', function (Blueprint $table) {
                if (!Schema::hasColumn('product_assets', 'infospot_id')) {
                    $table->unsignedBigInteger('infospot_id')->nullable();
                    $table->foreign('infospot_id')->references('id')->on('infospots')->onDelete('cascade');
                }
                if (Schema::hasColumn('product_assets', 'product_id')) {
                    $table->renameColumn('product_id', 'infospot_product_id');
                }
            });
            Schema::rename('product_assets', 'infospot_assets');
        }
        
        if (Schema::hasTable('products')) {
            Schema::rename('products', 'infospot_products');
        }
    }
};
