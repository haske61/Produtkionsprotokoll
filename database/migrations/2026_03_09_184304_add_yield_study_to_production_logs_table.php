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
        Schema::table('production_logs', function (Blueprint $table) {
            $table->decimal('yield_doppelbohnen', 8, 2)->nullable()->after('cacao_mass_produced_kg');
            $table->decimal('yield_steine', 8, 2)->nullable()->after('yield_doppelbohnen');
            $table->decimal('yield_schalen_in_nibs', 8, 2)->nullable()->after('yield_steine');
            $table->decimal('yield_nibs_in_schalen', 8, 2)->nullable()->after('yield_schalen_in_nibs');
            $table->decimal('yield_feuchtigkeit_nibs', 8, 2)->nullable()->after('yield_nibs_in_schalen');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('production_logs', function (Blueprint $table) {
            $table->dropColumn([
                'yield_doppelbohnen',
                'yield_steine',
                'yield_schalen_in_nibs',
                'yield_nibs_in_schalen',
                'yield_feuchtigkeit_nibs',
            ]);
        });
    }
};
