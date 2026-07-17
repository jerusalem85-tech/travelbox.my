<?php

namespace Database\Seeders;

use App\Models\Trip;
use App\Models\Customer;
use App\Models\FlightSegment;
use App\Models\HotelBooking;
use Illuminate\Database\Seeder;

class SampleBookingsSeeder extends Seeder
{
    public function run(): void
    {
        $customers = Customer::where('is_active', true)->pluck('id')->toArray();
        if (empty($customers)) {
            $this->command->error('No customers found. Run CustomerSeeder first.');
            return;
        }

        $supplierId = \App\Models\Supplier::first()?->id;
        $year = now()->format('Y');
        $last = Trip::where('trip_number', 'like', "T{$year}-%")
            ->orderBy('trip_number', 'desc')->first();
        $num = $last ? (int) substr($last->trip_number, 6) : 0;

        $bookings = [
            [
                'name' => 'Dubai Luxury Getaway', 'destination' => 'Dubai, UAE',
                'status' => 'confirmed', 'start_date' => '2026-08-20', 'end_date' => '2026-08-25',
                'lat' => 25.2048, 'lng' => 55.2708,
                'flight' => ['airline' => 'Emirates', 'flight_number' => 'EK501', 'from' => 'LHR', 'to' => 'DXB', 'dep' => '2026-08-20 08:30', 'arr' => '2026-08-20 19:00'],
                'hotel' => ['name' => 'Burj Al Arab', 'city' => 'Dubai', 'room' => 'Suite', 'check_in' => '2026-08-20', 'check_out' => '2026-08-25'],
            ],
            [
                'name' => 'Istanbul City Break', 'destination' => 'Istanbul, Turkey',
                'status' => 'confirmed', 'start_date' => '2026-09-10', 'end_date' => '2026-09-14',
                'lat' => 41.0082, 'lng' => 28.9784,
                'flight' => ['airline' => 'Turkish Airlines', 'flight_number' => 'TK763', 'from' => 'DXB', 'to' => 'IST', 'dep' => '2026-09-10 02:15', 'arr' => '2026-09-10 06:30'],
                'hotel' => ['name' => 'Hagia Sophia Mansions', 'city' => 'Istanbul', 'room' => 'Deluxe', 'check_in' => '2026-09-10', 'check_out' => '2026-09-14'],
            ],
            [
                'name' => 'Paris Romantic Escape', 'destination' => 'Paris, France',
                'status' => 'in_progress', 'start_date' => '2026-07-15', 'end_date' => '2026-07-20',
                'lat' => 48.8566, 'lng' => 2.3522,
                'flight' => ['airline' => 'Air France', 'flight_number' => 'AF115', 'from' => 'DXB', 'to' => 'CDG', 'dep' => '2026-07-15 09:00', 'arr' => '2026-07-15 14:15'],
                'hotel' => ['name' => 'Hotel Ritz Paris', 'city' => 'Paris', 'room' => 'Junior Suite', 'check_in' => '2026-07-15', 'check_out' => '2026-07-20'],
            ],
            [
                'name' => 'Bangkok Adventure', 'destination' => 'Bangkok, Thailand',
                'status' => 'enquiry', 'start_date' => '2026-10-05', 'end_date' => '2026-10-12',
                'lat' => 13.7563, 'lng' => 100.5018,
                'flight' => ['airline' => 'Qatar Airways', 'flight_number' => 'QR832', 'from' => 'DXB', 'to' => 'BKK', 'dep' => '2026-10-05 20:50', 'arr' => '2026-10-06 06:20'],
                'hotel' => ['name' => 'Mandarin Oriental Bangkok', 'city' => 'Bangkok', 'room' => 'River View', 'check_in' => '2026-10-06', 'check_out' => '2026-10-12'],
            ],
            [
                'name' => 'New York Business Summit', 'destination' => 'New York, USA',
                'status' => 'confirmed', 'start_date' => '2026-09-25', 'end_date' => '2026-09-30',
                'lat' => 40.7128, 'lng' => -74.0060,
                'flight' => ['airline' => 'Emirates', 'flight_number' => 'EK201', 'from' => 'DXB', 'to' => 'JFK', 'dep' => '2026-09-25 08:45', 'arr' => '2026-09-25 14:25'],
                'hotel' => ['name' => 'The Plaza Hotel', 'city' => 'New York', 'room' => 'Executive', 'check_in' => '2026-09-25', 'check_out' => '2026-09-30'],
            ],
            [
                'name' => 'Cairo & Nile Cruise', 'destination' => 'Cairo, Egypt',
                'status' => 'completed', 'start_date' => '2026-06-01', 'end_date' => '2026-06-08',
                'lat' => 30.0444, 'lng' => 31.2357,
                'flight' => ['airline' => 'EgyptAir', 'flight_number' => 'MS916', 'from' => 'DXB', 'to' => 'CAI', 'dep' => '2026-06-01 12:00', 'arr' => '2026-06-01 14:15'],
                'hotel' => ['name' => 'Mena House Hotel', 'city' => 'Cairo', 'room' => 'Pyramid View', 'check_in' => '2026-06-01', 'check_out' => '2026-06-08'],
            ],
            [
                'name' => 'Kuala Lumpur Family Trip', 'destination' => 'Kuala Lumpur, Malaysia',
                'status' => 'confirmed', 'start_date' => '2026-12-20', 'end_date' => '2026-12-27',
                'lat' => 3.1390, 'lng' => 101.6869,
                'flight' => ['airline' => 'Malaysia Airlines', 'flight_number' => 'MH161', 'from' => 'DXB', 'to' => 'KUL', 'dep' => '2026-12-20 01:50', 'arr' => '2026-12-20 12:55'],
                'hotel' => ['name' => 'Shangri-La Kuala Lumpur', 'city' => 'Kuala Lumpur', 'room' => 'Family Room', 'check_in' => '2026-12-20', 'check_out' => '2026-12-27'],
            ],
            [
                'name' => 'Casablanca Business Trip', 'destination' => 'Casablanca, Morocco',
                'status' => 'in_progress', 'start_date' => '2026-07-15', 'end_date' => '2026-07-18',
                'lat' => 33.5731, 'lng' => -7.5898,
                'flight' => ['airline' => 'Royal Air Maroc', 'flight_number' => 'AT261', 'from' => 'DXB', 'to' => 'CMN', 'dep' => '2026-07-15 06:45', 'arr' => '2026-07-15 12:00'],
                'hotel' => ['name' => 'Hyatt Regency Casablanca', 'city' => 'Casablanca', 'room' => 'Business', 'check_in' => '2026-07-15', 'check_out' => '2026-07-18'],
            ],
            [
                'name' => 'Singapore Shopping Festival', 'destination' => 'Singapore',
                'status' => 'enquiry', 'start_date' => '2026-11-10', 'end_date' => '2026-11-16',
                'lat' => 1.3521, 'lng' => 103.8198,
                'flight' => ['airline' => 'Singapore Airlines', 'flight_number' => 'SQ495', 'from' => 'DXB', 'to' => 'SIN', 'dep' => '2026-11-10 02:05', 'arr' => '2026-11-10 13:55'],
                'hotel' => ['name' => 'Marina Bay Sands', 'city' => 'Singapore', 'room' => 'Deluxe City', 'check_in' => '2026-11-10', 'check_out' => '2026-11-16'],
            ],
            [
                'name' => 'Maldives Honeymoon', 'destination' => 'Male, Maldives',
                'status' => 'confirmed', 'start_date' => '2026-09-05', 'end_date' => '2026-09-12',
                'lat' => 4.1755, 'lng' => 73.5093,
                'flight' => ['airline' => 'Flydubai', 'flight_number' => 'FZ1137', 'from' => 'DXB', 'to' => 'MLE', 'dep' => '2026-09-05 10:00', 'arr' => '2026-09-05 15:05'],
                'hotel' => ['name' => 'Soneva Fushi Resort', 'city' => 'Male', 'room' => 'Water Villa', 'check_in' => '2026-09-05', 'check_out' => '2026-09-12'],
            ],
        ];

        $sellingPrices = [18500, 8500, 22000, 12000, 28000, 9500, 16500, 7800, 19800, 35000];
        $costPrices = [12600, 5800, 15500, 8200, 19500, 6500, 11200, 5400, 13800, 24000];

        foreach ($bookings as $i => $data) {
            $num++;
            $customerId = $customers[$i % count($customers)];

            $trip = Trip::create([
                'trip_number' => "T{$year}-" . str_pad($num, 4, '0', STR_PAD_LEFT),
                'customer_id' => $customerId,
                'status' => $data['status'],
                'type' => 'package',
                'name' => $data['name'],
                'destination' => $data['destination'],
                'latitude' => $data['lat'],
                'longitude' => $data['lng'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'total_selling_price' => $sellingPrices[$i],
                'total_cost_price' => $costPrices[$i],
                'currency' => 'USD',
                'created_by' => 1,
            ]);

            FlightSegment::create([
                'trip_id' => $trip->id,
                'type' => 'departure',
                'airline' => $data['flight']['airline'],
                'flight_number' => $data['flight']['flight_number'],
                'departure_airport' => $data['flight']['from'],
                'arrival_airport' => $data['flight']['to'],
                'departure_datetime' => $data['flight']['dep'],
                'arrival_datetime' => $data['flight']['arr'],
                'supplier_id' => $supplierId,
                'status' => 'confirmed',
                'currency' => 'USD',
                'cost_price' => round($costPrices[$i] * 0.4, 2),
                'selling_price' => round($sellingPrices[$i] * 0.4, 2),
            ]);

            HotelBooking::create([
                'trip_id' => $trip->id,
                'hotel_name' => $data['hotel']['name'],
                'city' => $data['hotel']['city'],
                'check_in' => $data['hotel']['check_in'],
                'check_out' => $data['hotel']['check_out'],
                'room_type' => $data['hotel']['room'],
                'number_of_rooms' => 1,
                'supplier_id' => $supplierId,
                'status' => 'confirmed',
                'currency' => 'USD',
                'cost_price' => round($costPrices[$i] * 0.6, 2),
                'selling_price' => round($sellingPrices[$i] * 0.6, 2),
            ]);
        }

        $this->command->info('Seeded ' . count($bookings) . ' sample trips with flights and hotels.');
    }
}
