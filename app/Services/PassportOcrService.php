<?php

namespace App\Services;

use Smalot\PdfParser\Parser as PdfParser;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PassportOcrService
{
    public function extractAndParse(string $storagePath): array
    {
        $text = $this->extractText($storagePath);
        if (!$text) {
            return ['error' => 'Could not extract text from image'];
        }

        $mrz = $this->parseMrz($text);
        if ($mrz) {
            return $this->parsePassportData($mrz, $text);
        }

        return $this->fallbackParse($text);
    }

    private function extractText(string $storagePath): ?string
    {
        $fullPath = Storage::disk('public')->path($storagePath);
        if (!file_exists($fullPath)) return null;

        $mime = Storage::disk('public')->mimeType($storagePath);

        if ($mime === 'application/pdf') {
            try {
                $parser = new PdfParser;
                $text = $parser->parseFile($fullPath)->getText();
                if (strlen(trim(preg_replace('/\s+/', ' ', $text))) > 20) return $text;
            } catch (\Exception $e) {
                Log::warning('PDF extraction failed', ['err' => $e->getMessage()]);
            }
        }

        return $this->ocrImage($fullPath);
    }

    private function ocrImage(string $path): ?string
    {
        $apiKey = config('services.ocr.space_api_key');
        try {
            $request = Http::timeout(60)
                ->attach('file', file_get_contents($path), basename($path));
            if ($apiKey) $request->withHeaders(['apikey' => $apiKey]);
            $json = $request->post('https://api.ocr.space/parse/image', [
                'language' => 'eng',
                'isOverlayRequired' => false,
                'detectOrientation' => true,
                'scale' => true,
                'OCREngine' => 2,
            ])->json();

            if (($json['OCRExitCode'] ?? 0) === 1) {
                $parts = array_map(fn($r) => $r['ParsedText'] ?? '', $json['ParsedResults'] ?? []);
                return implode("\n", $parts);
            }
            Log::warning('OCR.space passport failed', ['exit' => $json['OCRExitCode'] ?? '']);
            return null;
        } catch (\Exception $e) {
            Log::error('OCR.space exception: ' . $e->getMessage());
            return null;
        }
    }

    private function parseMrz(string $text): ?array
    {
        $clean = preg_replace('/[^\w<]/', '', strtoupper($text));

        $lines = explode("\n", strtoupper($text));
        $lines = array_map('trim', $lines);
        $lines = array_values(array_filter($lines, fn($l) => strlen($l) > 20));

        $mrzLines = [];
        foreach ($lines as $line) {
            $flat = preg_replace('/[^A-Z0-9<]/', '', $line);
            if (preg_match('/^P<[A-Z]{3}<{1,}/', $flat) && strlen($flat) >= 30) {
                $mrzLines[0] = $flat;
            } elseif (preg_match('/^[A-Z0-9<]{20,}$/', $flat) && strlen($flat) >= 30) {
                if (!isset($mrzLines[0])) $mrzLines[0] = $flat;
                else $mrzLines[1] = $flat;
            }
        }

        if (count($mrzLines) >= 2) {
            return $mrzLines;
        }

        if (count($mrzLines) === 1 && strlen($mrzLines[0]) >= 60) {
            $mid = (int)(strlen($mrzLines[0]) / 2);
            return [
                0 => substr($mrzLines[0], 0, $mid),
                1 => substr($mrzLines[0], $mid),
            ];
        }

        $joined = str_replace(' ', '', $clean);
        if (preg_match('/(P<[A-Z]{3}<{1,}[A-Z<]+).{0,5}([A-Z0-9<]{20,})/', $joined, $m)) {
            return [$m[1], $m[2]];
        }

        return null;
    }

    private function parsePassportData(array $mrz, string $rawText): array
    {
        $line1 = $mrz[0] ?? '';
        $line2 = $mrz[1] ?? '';

        $surname = '';
        $givenNames = '';

        if (preg_match('/^P<[A-Z]{3}<<?(.+)$/', $line1, $m)) {
            $namePart = $m[1];
            $parts = explode('<<', $namePart);
            $surname = str_replace('<', ' ', $parts[0] ?? '');
            $surname = trim(preg_replace('/\s+/', ' ', $surname));
            $givenNames = isset($parts[1]) ? trim(str_replace('<', ' ', $parts[1])) : '';
        }

        $passportNumber = '';
        $nationality = '';
        $dob = '';
        $sex = '';
        $expiry = '';

        if (preg_match('/^([A-Z0-9<]{9})\d/', $line2, $m)) {
            $passportNumber = str_replace('<', '', $m[1]);
        }

        if (preg_match('/^\w+\d[A-Z]{3}([A-Z]{3})/', $line2, $m)) {
            $nationality = $m[1];
        }

        if (preg_match('/^\w+\d[A-Z]{3}[A-Z]{3}(\d{6})/', $line2, $m)) {
            $dob = $this->yymmddToDate($m[1]);
        }

        if (preg_match('/^\w+\d[A-Z]{3}[A-Z]{3}\d{6}\d([MF])/', $line2, $m)) {
            $sex = $m[1] === 'M' ? 'Male' : ($m[1] === 'F' ? 'Female' : '');
        }

        if (preg_match('/^\w+\d[A-Z]{3}[A-Z]{3}\d{6}\d[MF](\d{6})/', $line2, $m)) {
            $expiry = $this->yymmddToDate($m[1]);
        }

        $firstName = '';
        $lastName = $surname;
        if ($givenNames) {
            $names = explode(' ', $givenNames);
            $firstName = $names[0] ?? '';
        }

        return [
            'first_name' => $firstName ?: $this->guessFirstName($rawText, $lastName),
            'last_name' => $lastName ?: $this->guessLastName($rawText),
            'date_of_birth' => $dob,
            'nationality' => $nationality ?: $this->guessNationality($rawText),
            'passport_number' => $passportNumber ?: $this->guessPassportNumber($rawText),
            'passport_expiry' => $expiry,
            'sex' => $sex,
            'raw_text' => $rawText,
        ];
    }

    private function fallbackParse(string $text): array
    {
        $passportNumber = $this->guessPassportNumber($text);
        $dob = $this->guessDateOfBirth($text);
        $expiry = $this->guessExpiry($text);
        $lastName = $this->guessLastName($text);
        $firstName = $this->guessFirstName($text, $lastName);

        return [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'date_of_birth' => $dob,
            'nationality' => $this->guessNationality($text),
            'passport_number' => $passportNumber,
            'passport_expiry' => $expiry,
            'raw_text' => $text,
        ];
    }

    private function guessPassportNumber(string $text): ?string
    {
        if (preg_match('/(?:Passport[\s#]*No|Passport[\s#:]*|Passport Number|Document Number)[\s#:]*([A-Z0-9]{5,20})/i', $text, $m)) {
            return strtoupper($m[1]);
        }
        if (preg_match('/\b([A-Z]{1,2}\d{5,8})\b/', $text, $m)) {
            return $m[1];
        }
        return null;
    }

    private function guessDateOfBirth(string $text): ?string
    {
        $patterns = [
            '/(?:Date of Birth|DOB|Birth Date|Date of Birth)[\s:]*(\d{1,2})\s*(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)[a-z]*[\s,]*(\d{4})/i',
            '/(?:Date of Birth|DOB|Birth Date)[\s:]*(\d{4})-(\d{2})-(\d{2})/i',
            '/(?:Date of Birth|DOB|Birth Date)[\s:]*(\d{2})[\/.](\d{2})[\/.](\d{4})/i',
        ];
        foreach ($patterns as $p) {
            if (preg_match($p, $text, $m)) {
                if (isset($m[3]) && strlen($m[3]) === 4 && is_numeric($m[1]) && is_numeric($m[3])) {
                    $months = ['jan' => 1, 'feb' => 2, 'mar' => 3, 'apr' => 4, 'may' => 5, 'jun' => 6, 'jul' => 7, 'aug' => 8, 'sep' => 9, 'oct' => 10, 'nov' => 11, 'dec' => 12];
                    if (isset($months[strtolower($m[2])])) return sprintf('%04d-%02d-%02d', $m[3], $months[strtolower($m[2])], $m[1]);
                }
                if (isset($m[4]) && strlen($m[4]) === 4) return "$m[4]-$m[2]-$m[1]";
                if (isset($m[1]) && strlen($m[1]) === 4) return "$m[1]-$m[2]-$m[3]";
            }
        }
        return null;
    }

    private function guessExpiry(string $text): ?string
    {
        $patterns = [
            '/(?:Date of Expiry|Expiry Date|Expiration Date|Valid Until)[\s:]*(\d{1,2})\s*(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)[a-z]*[\s,]*(\d{4})/i',
            '/(?:Date of Expiry|Expiry Date|Expiration Date|Valid Until)[\s:]*(\d{4})-(\d{2})-(\d{2})/i',
            '/(?:Date of Expiry|Expiry Date|Expiration Date|Valid Until)[\s:]*(\d{2})[\/.](\d{2})[\/.](\d{4})/i',
        ];
        foreach ($patterns as $p) {
            if (preg_match($p, $text, $m)) {
                if (isset($m[3]) && strlen($m[3]) === 4 && is_numeric($m[1]) && is_numeric($m[3])) {
                    $months = ['jan' => 1, 'feb' => 2, 'mar' => 3, 'apr' => 4, 'may' => 5, 'jun' => 6, 'jul' => 7, 'aug' => 8, 'sep' => 9, 'oct' => 10, 'nov' => 11, 'dec' => 12];
                    if (isset($months[strtolower($m[2])])) return sprintf('%04d-%02d-%02d', $m[3], $months[strtolower($m[2])], $m[1]);
                }
                if (isset($m[4]) && strlen($m[4]) === 4) return "$m[4]-$m[2]-$m[1]";
                if (isset($m[1]) && strlen($m[1]) === 4) return "$m[1]-$m[2]-$m[3]";
            }
        }
        return null;
    }

    private function guessNationality(string $text): ?string
    {
        if (preg_match('/(?:Nationality)[\s:]*([A-Z][a-z]+(?:\s[A-Z][a-z]+)?)/', $text, $m)) return $m[1];
        $countries = [
            'JORDANIAN' => 'Jordan', 'JORDAN' => 'Jordan', 'EGYPTIAN' => 'Egypt',
            'EGYPT' => 'Egypt', 'PALESTINIAN' => 'Palestine', 'PALESTINE' => 'Palestine',
            'SYRIAN' => 'Syria', 'SYRIA' => 'Syria', 'LEBANESE' => 'Lebanon',
            'LEBANON' => 'Lebanon', 'IRAQI' => 'Iraq', 'IRAQ' => 'Iraq',
            'SAUDI' => 'Saudi Arabia', 'TURKISH' => 'Turkey', 'TURKEY' => 'Turkey',
            'EMIRATI' => 'UAE', 'QATARI' => 'Qatar', 'KUWAITI' => 'Kuwait',
            'OMANI' => 'Oman', 'BAHRAINI' => 'Bahrain', 'AMERICAN' => 'United States',
            'BRITISH' => 'United Kingdom', 'CANADIAN' => 'Canada', 'GERMAN' => 'Germany',
        ];
        foreach ($countries as $key => $value) {
            if (str_contains(strtoupper($text), $key)) return $value;
        }
        return null;
    }

    private function guessLastName(string $text): ?string
    {
        if (preg_match('/(?:Surname|Last Name|Family Name)[\s:]*([A-Z][a-z]+)/', $text, $m)) return $m[1];
        if (preg_match('/\b(Mr|Ms|Mrs|Dr)[.\s]+([A-Z][a-z]+)\s+([A-Z][a-z]+)/', $text, $m)) return $m[3];
        return null;
    }

    private function guessFirstName(string $text, ?string $lastName): ?string
    {
        if (preg_match('/(?:Given Name|First Name)[\s:]*([A-Z][a-z]+)/', $text, $m)) return $m[1];
        if (preg_match('/\b(Mr|Ms|Mrs|Dr)[.\s]+([A-Z][a-z]+)\s+([A-Z][a-z]+)/', $text, $m)) return $m[2];
        if ($lastName) {
            if (preg_match('/\b([A-Z][a-z]+)\s+' . preg_quote($lastName, '/') . '/', $text, $m)) return $m[1];
        }
        return null;
    }

    private function yymmddToDate(string $yymmdd): ?string
    {
        if (strlen($yymmdd) !== 6 || !is_numeric($yymmdd)) return null;
        $yy = (int)substr($yymmdd, 0, 2);
        $mm = (int)substr($yymmdd, 2, 2);
        $dd = (int)substr($yymmdd, 4, 2);
        $yyyy = $yy > 30 ? 1900 + $yy : 2000 + $yy;
        return sprintf('%04d-%02d-%02d', $yyyy, $mm, $dd);
    }
}
