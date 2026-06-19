<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE utilisateurs MODIFY role ENUM('admin','particulier','entreprise','bloque') DEFAULT 'particulier'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::table('utilisateurs')->where('role', 'bloque')->update(['role' => 'particulier']);
            DB::statement("ALTER TABLE utilisateurs MODIFY role ENUM('admin','particulier','entreprise') DEFAULT 'particulier'");
        }
    }
};
