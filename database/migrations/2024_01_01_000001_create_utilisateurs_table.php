<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('utilisateurs', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('pass');
            $table->string('nom');
            $table->string('prenom');
            $table->enum('role', ['admin', 'particulier', 'entreprise'])->default('particulier');
            $table->timestamp('date_inscription')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('utilisateurs');
    }
};
