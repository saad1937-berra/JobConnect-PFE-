<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competances', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Table pivot pour la relation many-to-many entre particuliers et competances
        Schema::create('particulier_competance', function (Blueprint $table) {
            $table->foreignId('particulier_id')->constrained('particuliers')->onDelete('cascade');
            $table->foreignId('competance_id')->constrained('competances')->onDelete('cascade');
            $table->primary(['particulier_id', 'competance_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('particulier_competance');
        Schema::dropIfExists('competances');
    }
};
