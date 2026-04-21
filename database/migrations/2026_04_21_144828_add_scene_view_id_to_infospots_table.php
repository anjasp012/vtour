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
            $table->foreignId('scene_view_id')->after('scene_id')->nullable()->constrained()->onDelete('cascade');
        });

        // Migrate existing data: Link hotspots to the primary view of their current scene
        $infospots = DB::table('infospots')->get();
        foreach ($infospots as $spot) {
            $primaryView = DB::table('scene_views')
                ->where('scene_id', $spot->scene_id)
                ->where('is_primary', true)
                ->first();

            if ($primaryView) {
                DB::table('infospots')
                    ->where('id', $spot->id)
                    ->update(['scene_view_id' => $primaryView->id]);
            } else {
                // If no primary view found, link to the first available view
                $firstView = DB::table('scene_views')
                    ->where('scene_id', $spot->scene_id)
                    ->first();
                if ($firstView) {
                    DB::table('infospots')
                        ->where('id', $spot->id)
                        ->update(['scene_view_id' => $firstView->id]);
                }
            }
        }

        // Drop original scene_id
        Schema::table('infospots', function (Blueprint $table) {
            $table->dropForeign(['scene_id']);
            $table->dropColumn('scene_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('infospots', function (Blueprint $table) {
            $table->foreignId('scene_id')->after('scene_view_id')->nullable()->constrained()->onDelete('cascade');
        });

        // Restore scene_id from scene_view_id
        $infospots = DB::table('infospots')->get();
        foreach ($infospots as $spot) {
            $view = DB::table('scene_views')->find($spot->scene_view_id);
            if ($view) {
                DB::table('infospots')
                    ->where('id', $spot->id)
                    ->update(['scene_id' => $view->scene_id]);
            }
        }

        Schema::table('infospots', function (Blueprint $table) {
            $table->dropForeign(['scene_view_id']);
            $table->dropColumn('scene_view_id');
        });
    }
};
