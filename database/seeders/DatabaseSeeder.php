<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        DB::table('offre_competance')->truncate();
        DB::table('particulier_competance')->truncate();
        DB::table('particulier_offre')->truncate();
        DB::table('reports')->truncate();
        DB::table('messages')->truncate();
        DB::table('conversations')->truncate();
        DB::table('notifications')->truncate();
        DB::table('candidatures')->truncate();
        DB::table('cvs')->truncate();
        DB::table('offres')->truncate();
        DB::table('entreprises')->truncate();
        DB::table('particuliers')->truncate();
        DB::table('utilisateurs')->truncate();
        DB::table('categories')->truncate();
        DB::table('competances')->truncate();

        Schema::enableForeignKeyConstraints();

        $this->call([
            CategorieSeeder::class,
            CompetanceSeeder::class,
            UtilisateurSeeder::class,
            EntrepriseSeeder::class,
            OffreSeeder::class,
        ]);
    }
}
