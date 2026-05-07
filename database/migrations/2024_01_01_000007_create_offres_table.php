<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offres', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entreprise_id')->constrained('entreprises')->onDelete('cascade');
            $table->foreignId('categorie_id')->constrained('categories')->onDelete('restrict');
            $table->string('titre');
            $table->text('description')->nullable();
            $table->timestamp('date_publication')->useCurrent();
            $table->timestamp('date_expiration')->nullable();
            $table->string('contrat')->nullable(); // CDI, CDD, Stage...
            $table->string('duree')->nullable();
            $table->string('localisation')->nullable();
            $table->string('niveau_etude')->nullable();
            $table->enum('statut', ['active', 'expiree', 'brouillon'])->default('brouillon');
            $table->string('salaire')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offres');
    }
};
