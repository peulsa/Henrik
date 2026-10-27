<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('items')->whereNull('location_id')->orderBy('id')->each(function (object $item): void {
            $locationId = DB::table('stocks')
                ->where('item_id', $item->id)
                ->orderByDesc('quantity')
                ->orderBy('id')
                ->value('location_id');

            if ($locationId) {
                DB::table('items')->where('id', $item->id)->update(['location_id' => $locationId]);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // La ubicación existente se conserva al revertir para no perder datos.
    }
};
