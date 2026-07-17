<?php

namespace App\Services;

class FlightParserService
{
    public function parse(string $text): array
    {
        return [
            'booking_reference' => $this->extractBookingReference($text),
            'passengers' => $this->extractPassengers($text),
            'segments' => $this->extractSegments($text),
            'raw_text' => $text,
        ];
    }

    private function extractBookingReference(string $text): ?string
    {
        $patterns = [
            '/(?:PNR|Booking Reference|Booking Code|Confirmation Code|Record Locator|Reservation Code|Confirmation Number)[:\s]*([A-Z0-9]{5,7})/i',
            '/(?:Reference|Locator)[:\s]+([A-Z0-9]{5,7})\b/i',
            '/\b([A-Z0-9]{6})\b(?!\s*\d)/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $m)) return strtoupper($m[1]);
        }
        return null;
    }

    private function extractPassengers(string $text): array
    {
        $names = [];
        $patterns = [
            '/(?:Passengers?|Guests?|Names?|Travellers?)[:\s]*((?:Mr|Ms|Mrs|Dr|Prof)[a-z.]*\s+[A-Z][a-z]+(?:\s+[A-Z][a-z]+)?(?:\s*[,;]\s*(?:Mr|Ms|Mrs|Dr|Prof)[a-z.]*\s+[A-Z][a-z]+(?:\s+[A-Z][a-z]+)?)*)/i',
            '/\b(Mr|Ms|Mrs|Dr|Prof)[.\s]+([A-Z][a-z]+)\s+([A-Z][a-z]+)/',
        ];

        if (preg_match($patterns[0], $text, $m)) {
            $parts = preg_split('/[,;]/', $m[1]);
            foreach ($parts as $part) {
                $name = trim($part);
                if (strlen($name) > 3) $names[] = $name;
            }
        }

        if (preg_match_all($patterns[1], $text, $matches)) {
            foreach ($matches[0] as $i => $full) {
                $name = trim($full);
                if (!in_array($name, $names)) $names[] = $name;
            }
        }

        return array_unique($names);
    }

    private function extractSegments(string $text): array
    {
        $segments = [];
        $lines = preg_split('/\r\n|\n|\r/', $text);

        $airlineCodes = $this->getAirlineCodes();
        $airportCodes = $this->getAirportCodes();

        foreach ($lines as $line) {
            $segment = $this->parseSegmentLine($line, $airlineCodes, $airportCodes);
            if ($segment) $segments[] = $segment;
        }

        if (empty($segments)) {
            $segments = $this->extractSegmentsFromFlightText($text, $airlineCodes, $airportCodes);
        }

        return $segments;
    }

    private function parseSegmentLine(string $line, array $airlineCodes, array $airportCodes): ?array
    {
        $upper = strtoupper($line);

        if (!preg_match('/\b([A-Z]{2})\s*(\d{1,4})\b/', $upper, $f)) return null;
        $flightNumber = $f[1] . $f[2];
        $airlineCode = $f[1];

        if (!isset($airlineCodes[$airlineCode])) return null;

        $airportCodesFound = [];
        preg_match_all('/\b([A-Z]{3})\b/', $upper, $ap);
        foreach ($ap[1] as $code) {
            if (isset($airportCodes[$code]) && !in_array($code, $airportCodesFound)) {
                $airportCodesFound[] = $code;
            }
        }

        $departure = $airportCodesFound[0] ?? null;
        $arrival = $airportCodesFound[1] ?? null;

        $date = $this->extractDate($line);
        $times = $this->extractTimes($line);
        $bookingRef = $this->extractInlineBookingRef($line);

        return [
            'airline' => $airlineCodes[$airlineCode],
            'flight_number' => $flightNumber,
            'departure_airport' => $departure,
            'arrival_airport' => $arrival,
            'departure_datetime' => $date ? $date . 'T' . ($times[0] ?? '00:00') : null,
            'arrival_datetime' => $date ? $date . 'T' . ($times[1] ?? '23:59') : null,
            'booking_reference' => $bookingRef,
        ];
    }

    private function extractSegmentsFromFlightText(string $text, array $airlineCodes, array $airportCodes): array
    {
        $segments = [];

        preg_match_all('/\b([A-Z]{2})\s*(\d{1,4})\b/', $text, $flights);
        foreach ($flights[0] as $i => $flight) {
            $code = $flights[1][$i];
            $num = $flights[2][$i];
            $flightNum = $code . $num;

            if (!isset($airlineCodes[$code])) continue;

            $airportCodesFound = [];
            preg_match_all('/\b([A-Z]{3})\b/', $text, $ap);
            foreach ($ap[1] as $ac) {
                if (isset($airportCodes[$ac]) && !in_array($ac, $airportCodesFound)) {
                    $airportCodesFound[] = $ac;
                }
            }

            $departureIdx = $i * 2;
            $arrivalIdx = $departureIdx + 1;

            $segments[] = [
                'airline' => $airlineCodes[$code],
                'flight_number' => $flightNum,
                'departure_airport' => $airportCodesFound[$departureIdx] ?? null,
                'arrival_airport' => $airportCodesFound[$arrivalIdx] ?? null,
                'departure_datetime' => null,
                'arrival_datetime' => null,
                'booking_reference' => null,
            ];
        }

        return $segments;
    }

    private function extractDate(string $text): ?string
    {
        $patterns = [
            '/\b(\d{1,2})\s+(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)[a-z]*\s+(\d{4})\b/i',
            '/\b(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)[a-z]*\s+(\d{1,2}),?\s*(\d{4})\b/i',
            '/\b(\d{4})-(\d{2})-(\d{2})\b/',
            '/\b(\d{2})[\/.](\d{2})[\/.](\d{4})\b/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $m)) {
                if (is_numeric($m[1]) && isset($m[3]) && is_numeric($m[3])) {
                    $months = ['jan' => 1, 'feb' => 2, 'mar' => 3, 'apr' => 4, 'may' => 5, 'jun' => 6,
                        'jul' => 7, 'aug' => 8, 'sep' => 9, 'oct' => 10, 'nov' => 11, 'dec' => 12];
                    $month = strtolower($m[2]);
                    if (isset($months[$month])) {
                        return sprintf('%04d-%02d-%02d', $m[3], $months[$month], $m[1]);
                    }
                }
                if (isset($months) && isset($months[strtolower($m[1])])) {
                    return sprintf('%04d-%02d-%02d', $m[3], $months[strtolower($m[1])], $m[2]);
                }
                if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $m[0])) return $m[0];
                if (isset($m[3]) && strlen($m[3]) === 4) return "{$m[3]}-{$m[1]}-{$m[2]}";
            }
        }

        return null;
    }

    private function extractTimes(string $text): array
    {
        $times = [];
        preg_match_all('/\b(\d{1,2}):(\d{2})\s*(AM|PM)?\b/i', $text, $m, PREG_SET_ORDER);
        foreach ($m as $match) {
            $h = (int)$match[1];
            $min = $match[2];
            $ampm = strtoupper($match[3] ?? '');
            if ($ampm === 'PM' && $h < 12) $h += 12;
            if ($ampm === 'AM' && $h === 12) $h = 0;
            $times[] = sprintf('%02d:%s', $h, $min);
        }
        return $times;
    }

    private function extractInlineBookingRef(string $text): ?string
    {
        if (preg_match('/(?:Ref|BK|Confirmation)[:\s]*([A-Z0-9]{5,7})\b/i', $text, $m)) {
            return strtoupper($m[1]);
        }
        return null;
    }

    private function getAirlineCodes(): array
    {
        return [
            'TK' => 'Turkish Airlines', 'PC' => 'Pegasus', 'EK' => 'Emirates',
            'QR' => 'Qatar Airways', 'EY' => 'Etihad', 'SV' => 'Saudia',
            'WY' => 'Oman Air', 'GF' => 'Gulf Air', 'KU' => 'Kuwait Airways',
            'RJ' => 'Royal Jordanian', 'FZ' => 'Flydubai', 'G9' => 'Air Arabia',
            'J9' => 'Jazeera Airways', 'MS' => 'EgyptAir', 'SM' => 'Air Cairo',
            'ME' => 'Middle East Airlines', 'LH' => 'Lufthansa', 'BA' => 'British Airways',
            'AF' => 'Air France', 'KL' => 'KLM', 'IB' => 'Iberia',
            'AZ' => 'ITA Airways', 'A3' => 'Aegean Airlines', 'RO' => 'Tarom',
            'NH' => 'ANA', 'SQ' => 'Singapore Airlines', 'CX' => 'Cathay Pacific',
            'JL' => 'Japan Airlines', 'KE' => 'Korean Air', 'CA' => 'Air China',
            'MU' => 'China Eastern', 'CZ' => 'China Southern', 'DL' => 'Delta',
            'AA' => 'American Airlines', 'UA' => 'United Airlines', 'AC' => 'Air Canada',
            'LY' => 'El Al', 'ET' => 'Ethiopian Airlines', 'AT' => 'Royal Air Maroc',
            'TU' => 'Tunisair', 'AH' => 'Air Algerie', 'PK' => 'Pakistan International',
            'UL' => 'SriLankan Airlines', '3L' => 'Air Arabia Abu Dhabi',
        ];
    }

    private function getAirportCodes(): array
    {
        return [
            'AMM' => 'Amman', 'AQJ' => 'Aqaba', 'ADB' => 'Izmir', 'AMS' => 'Amsterdam',
            'ATH' => 'Athens', 'AUH' => 'Abu Dhabi', 'BAH' => 'Bahrain', 'BCN' => 'Barcelona',
            'BEY' => 'Beirut', 'BKK' => 'Bangkok', 'BSL' => 'Basel', 'BUD' => 'Budapest',
            'CAI' => 'Cairo', 'CMN' => 'Casablanca', 'CPH' => 'Copenhagen', 'DOH' => 'Doha',
            'DUB' => 'Dublin', 'DXB' => 'Dubai', 'FCO' => 'Rome', 'FRA' => 'Frankfurt',
            'GVA' => 'Geneva', 'HAM' => 'Hamburg', 'HEL' => 'Helsinki', 'HKT' => 'Phuket',
            'HRG' => 'Hurghada', 'IST' => 'Istanbul', 'SAW' => 'Istanbul Sabiha',
            'JED' => 'Jeddah', 'JFK' => 'New York JFK', 'KIV' => 'Chisinau',
            'KRT' => 'Khartoum', 'KUL' => 'Kuala Lumpur', 'LCA' => 'Larnaca',
            'LHR' => 'London Heathrow', 'LGW' => 'London Gatwick', 'LTN' => 'London Luton',
            'STN' => 'London Stansted', 'LOS' => 'Lagos', 'MAD' => 'Madrid',
            'MCT' => 'Muscat', 'MIL' => 'Milan', 'MUC' => 'Munich', 'MXP' => 'Milan Malpensa',
            'NBO' => 'Nairobi', 'NCE' => 'Nice', 'ORD' => 'Chicago', 'OTP' => 'Bucharest',
            'PAR' => 'Paris', 'CDG' => 'Paris Charles de Gaulle', 'ORY' => 'Paris Orly',
            'PRG' => 'Prague', 'RAK' => 'Marrakech', 'Riy' => 'Riyadh',
            'RUH' => 'Riyadh', 'SEA' => 'Seattle', 'SIN' => 'Singapore',
            'SOF' => 'Sofia', 'SSH' => 'Sharm El Sheikh', 'TBS' => 'Tbilisi',
            'TLV' => 'Tel Aviv', 'TUN' => 'Tunis', 'VIE' => 'Vienna',
            'WAW' => 'Warsaw', 'ZRH' => 'Zurich', 'EVN' => 'Yerevan',
        ];
    }
}
