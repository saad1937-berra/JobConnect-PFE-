<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('conversations')->onDelete('cascade');
            $table->foreignId('reporter_id')->constrained('utilisateurs')->onDelete('cascade');
            $table->foreignId('reported_id')->constrained('utilisateurs')->onDelete('cascade');
            $table->text('reason')->nullable();
            $table->enum('status', ['nouveau', 'en_cours', 'traite', 'rejete'])->default('nouveau');
            $table->text('admin_note')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
