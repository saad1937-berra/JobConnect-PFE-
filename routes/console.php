<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\Cv;
use App\Services\CvTextExtractor;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('cv:extract-text {--force : Reextraire meme si cv_text existe deja}', function () {
    $query = Cv::query();

    if (!$this->option('force')) {
        $query->where(function ($q) {
            $q->whereNull('cv_text')->orWhere('cv_text', '');
        });
    }

    $updated = 0;
    $failed = 0;

    $query->chunkById(50, function ($cvs) use (&$updated, &$failed) {
        foreach ($cvs as $cv) {
            $text = CvTextExtractor::fromStoragePath($cv->cv_path, 'local');

            if ($text === null) {
                $failed++;
                continue;
            }

            $cv->update(['cv_text' => $text]);
            $updated++;
        }
    });

    $this->info("CV traites: {$updated}");

    if ($failed > 0) {
        $this->warn("CV non lisibles: {$failed}");
    }
})->purpose('Extraire le texte des CV stockes pour le matching');
