<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Table de relation many-to-many Particulier <-> Offre (avec type de recommandation)
        Schema::create('particulier_offre', function (Blueprint $table) {
            $table->id();
            $table->foreignId('particulier_id')->constrained('particuliers')->onDelete('cascade');
            $table->foreignId('offre_id')->constrained('offres')->onDelete('cascade');
            $table->string('type')->nullable(); // type de recommandation (ex: suggérée, sauvegardée...)
            $table->timestamps();

            $table->unique(['particulier_id', 'offre_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('particulier_offre');
    }
};
