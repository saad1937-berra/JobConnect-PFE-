<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use ZipArchive;

class CvTextExtractor
{
    public static function fromPublicPath(string $path): ?string
    {
        return self::fromStoragePath($path, 'public');
    }

    public static function fromStoragePath(string $path, string $disk = 'local'): ?string
    {
        if (!Storage::disk('public')->exists($path)) {
            if ($disk === 'public' || !Storage::disk($disk)->exists($path)) {
                return null;
            }
        }

        $storageDisk = Storage::disk($disk)->exists($path) ? $disk : 'public';
        $absolutePath = Storage::disk($storageDisk)->path($path);
        $extension = strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION));

        $text = match ($extension) {
            'txt' => @file_get_contents($absolutePath) ?: '',
            'docx' => self::fromDocx($absolutePath),
            'pdf' => self::fromPdf($absolutePath),
            default => '',
        };

        $text = self::normalize($text);

        return $text !== '' ? $text : null;
    }

    private static function fromDocx(string $absolutePath): string
    {
        if (!class_exists(ZipArchive::class)) {
            return '';
        }

        $zip = new ZipArchive();
        if ($zip->open($absolutePath) !== true) {
            return '';
        }

        $xml = $zip->getFromName('word/document.xml') ?: '';
        $zip->close();

        if ($xml === '') {
            return '';
        }

        $xml = preg_replace('/<\/w:p>/', "\n", $xml);
        $xml = strip_tags($xml);

        return html_entity_decode($xml, ENT_QUOTES | ENT_XML1, 'UTF-8');
    }

    private static function fromPdf(string $absolutePath): string
    {
        $text = self::fromPdfToTextCommand($absolutePath);

        if ($text !== '') {
            return $text;
        }

        $contents = @file_get_contents($absolutePath) ?: '';
        if ($contents === '') {
            return '';
        }

        preg_match_all('/\((?:\\\\.|[^\\\\)])*\)\s*Tj/s', $contents, $singleTextMatches);
        preg_match_all('/\[(.*?)\]\s*TJ/s', $contents, $arrayTextMatches);

        $chunks = [];
        foreach ($singleTextMatches[0] ?? [] as $match) {
            $chunks[] = self::decodePdfString($match);
        }

        foreach ($arrayTextMatches[1] ?? [] as $match) {
            preg_match_all('/\((?:\\\\.|[^\\\\)])*\)/s', $match, $strings);
            foreach ($strings[0] ?? [] as $string) {
                $chunks[] = self::decodePdfString($string);
            }
        }

        return implode(' ', $chunks);
    }

    private static function fromPdfToTextCommand(string $absolutePath): string
    {
        if (!function_exists('shell_exec')) {
            return '';
        }

        $binary = self::pdftotextBinary();

        if ($binary === null) {
            return '';
        }

        $command = escapeshellarg($binary) . ' -layout ' . escapeshellarg($absolutePath) . ' - 2>NUL';
        $output = @shell_exec($command);

        return is_string($output) ? $output : '';
    }

    private static function pdftotextBinary(): ?string
    {
        $candidates = [
            'pdftotext',
            'C:\\poppler\\Library\\bin\\pdftotext.exe',
        ];

        foreach ($candidates as $candidate) {
            if ($candidate === 'pdftotext') {
                $output = @shell_exec('where pdftotext 2>NUL');
                $path = trim((string) $output);

                if ($path !== '') {
                    return strtok($path, PHP_EOL) ?: 'pdftotext';
                }

                continue;
            }

            if (is_file($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    private static function decodePdfString(string $value): string
    {
        $value = preg_replace('/^\[|\]\s*TJ$|\)\s*Tj$/', '', $value);
        $value = trim($value);

        if (str_starts_with($value, '(') && str_ends_with($value, ')')) {
            $value = substr($value, 1, -1);
        }

        return stripcslashes($value);
    }

    public static function normalize(?string $text): string
    {
        $text = $text ?? '';
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/[^\P{C}\n\t]+/u', ' ', $text) ?? $text;
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;

        return trim($text);
    }
}
