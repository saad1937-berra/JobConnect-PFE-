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
        Schema::create('offre_competance', function (Blueprint $table) {
            $table->foreignId('offre_id')->constrained('offres')->onDelete('cascade');
            $table->foreignId('competance_id')->constrained('competances')->onDelete('cascade');
            $table->primary(['offre_id', 'competance_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offre_competance');
    }
};
