<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('particuliers', function (Blueprint $table) {
            $table->string('cv_titre')->nullable()->after('niveau_etude');
            $table->json('cv_experiences')->nullable()->after('cv_titre');
            $table->json('cv_formations')->nullable()->after('cv_experiences');
            $table->json('cv_langues')->nullable()->after('cv_formations');
            $table->json('cv_loisirs')->nullable()->after('cv_langues');
        });
    }

    public function down(): void
    {
        Schema::table('particuliers', function (Blueprint $table) {
            $table->dropColumn([
                'cv_titre',
                'cv_experiences',
                'cv_formations',
                'cv_langues',
                'cv_loisirs',
            ]);
        });
    }
};
