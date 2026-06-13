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
        Schema::table('particulier_competance', function (Blueprint $table) {
            $table->enum('niveau', ['Débutant', 'Intermédiaire', 'Avancé', 'Expert'])
                ->default('Intermédiaire')
                ->after('competance_id');
        });
    }

    public function down(): void
    {
        Schema::table('particulier_competance', function (Blueprint $table) {
            $table->dropColumn('niveau');
        });
    }
};
