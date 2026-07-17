<?php

namespace App\Services;

use Smalot\PdfParser\Parser as PdfParser;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FlightOcrService
{
    public function extractText(string $storagePath): ?string
    {
        $fullPath = Storage::disk('public')->path($storagePath);
        if (!file_exists($fullPath)) return null;

        $mime = Storage::disk('public')->mimeType($storagePath);

        if (str_starts_with($mime, 'image/')) {
            return $this->ocrImage($fullPath);
        }

        if ($mime === 'application/pdf') {
            $text = $this->extractPdfText($fullPath);
            $clean = trim(preg_replace('/\s+/', ' ', $text));
            if (strlen($clean) > 20) return $text;
            return $this->ocrImage($fullPath);
        }

        return null;
    }

    private function extractPdfText(string $path): string
    {
        try {
            $parser = new PdfParser;
            $pdf = $parser->parseFile($path);
            return $pdf->getText();
        } catch (\Exception $e) {
            Log::warning('PDF text extraction failed: ' . $e->getMessage());
            return '';
        }
    }

    private function ocrImage(string $path): ?string
    {
        $apiKey = config('services.ocr.space_api_key');
        $payload = [
            'language' => 'eng',
            'isOverlayRequired' => false,
            'detectOrientation' => true,
            'scale' => true,
            'OCREngine' => 2,
        ];

        try {
            $request = Http::timeout(60)
                ->attach('file', file_get_contents($path), basename($path));

            if ($apiKey) {
                $request->withHeaders(['apikey' => $apiKey]);
            }

            $response = $request->post('https://api.ocr.space/parse/image', $payload);
            $json = $response->json();

            if (($json['OCRExitCode'] ?? 0) === 1) {
                $parts = [];
                foreach ($json['ParsedResults'] ?? [] as $result) {
                    $parts[] = $result['ParsedText'] ?? '';
                }
                return implode("\n", $parts);
            }

            Log::warning('OCR.space failed', [
                'exit' => $json['OCRExitCode'] ?? 'unknown',
                'err' => $json['ErrorMessage'] ?? '',
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error('OCR.space exception: ' . $e->getMessage());
            return null;
        }
    }
}
