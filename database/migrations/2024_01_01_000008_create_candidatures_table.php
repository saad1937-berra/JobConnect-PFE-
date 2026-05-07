<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('particulier_id')->constrained('particuliers')->onDelete('cascade');
            $table->foreignId('offre_id')->constrained('offres')->onDelete('cascade');
            $table->timestamp('date')->useCurrent();
            $table->enum('statut', ['en_attente', 'acceptee', 'refusee', 'en_cours'])->default('en_attente');
            $table->text('commentaire')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidatures');
    }
};
