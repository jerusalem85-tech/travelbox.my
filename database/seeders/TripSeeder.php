<?php

namespace Database\Seeders;

use App\Models\Trip;
use App\Models\Customer;
use Illuminate\Database\Seeder;

class TripSeeder extends Seeder
{
    public function run(): void
    {
        $customers = Customer::where('is_active', true)->pluck('id')->toArray();
        if (empty($customers)) {
            $this->command->error('No customers found. Run CustomerSeeder first.');
            return;
        }

        $year = now()->format('Y');
        $last = Trip::where('trip_number', 'like', "T{$year}-%")
            ->orderBy('trip_number', 'desc')->first();
        $num = $last ? (int) substr($last->trip_number, 6) : 0;

        $trips = [
            ['status' => 'confirmed', 'type' => 'leisure',     'name' => 'Dubai Family Holiday',    'destination' => 'Dubai, UAE',             'start_date' => '2026-08-15', 'end_date' => '2026-08-22', 'total_selling_price' => 18500.00, 'total_cost_price' => 12600.00, 'currency' => 'AED'],
            ['status' => 'in_progress', 'type' => 'business',   'name' => 'London Business Trip',    'destination' => 'London, UK',             'start_date' => '2026-07-01', 'end_date' => '2026-07-07', 'total_selling_price' => 22000.00, 'total_cost_price' => 15800.00, 'currency' => 'GBP'],
            ['status' => 'enquiry',     'type' => 'honeymoon',   'name' => 'Maldives Honeymoon',      'destination' => 'Male, Maldives',         'start_date' => '2026-10-01', 'end_date' => '2026-10-08', 'total_selling_price' => 35000.00, 'total_cost_price' => 24000.00, 'currency' => 'USD'],
            ['status' => 'completed',   'type' => 'leisure',     'name' => 'Saudi Umrah Trip',        'destination' => 'Makkah, Saudi Arabia',   'start_date' => '2026-03-10', 'end_date' => '2026-03-18', 'total_selling_price' => 8900.00,  'total_cost_price' => 6200.00,  'currency' => 'SAR'],
            ['status' => 'confirmed',   'type' => 'corporate',   'name' => 'Team Building Retreat',   'destination' => 'Ras Al Khaimah, UAE',    'start_date' => '2026-09-05', 'end_date' => '2026-09-07', 'total_selling_price' => 45000.00, 'total_cost_price' => 31000.00, 'currency' => 'AED'],
            ['status' => 'enquiry',     'type' => 'leisure',     'name' => 'Egypt Nile Cruise',       'destination' => 'Cairo/Luxor, Egypt',     'start_date' => '2026-11-15', 'end_date' => '2026-11-22', 'total_selling_price' => 12000.00, 'total_cost_price' => 8400.00,  'currency' => 'USD'],
            ['status' => 'confirmed',   'type' => 'leisure',     'name' => 'Swiss Alps Ski Trip',     'destination' => 'Zermatt, Switzerland',   'start_date' => '2026-12-20', 'end_date' => '2026-12-28', 'total_selling_price' => 28000.00, 'total_cost_price' => 19500.00, 'currency' => 'CHF'],
            ['status' => 'completed',   'type' => 'business',   'name' => 'Qatar Expo Visit',         'destination' => 'Doha, Qatar',            'start_date' => '2026-02-20', 'end_date' => '2026-02-23', 'total_selling_price' => 6800.00,  'total_cost_price' => 4800.00,  'currency' => 'QAR'],
            ['status' => 'cancelled',   'type' => 'leisure',     'name' => 'Japan Cherry Blossom',    'destination' => 'Tokyo/Osaka, Japan',     'start_date' => '2026-04-01', 'end_date' => '2026-04-10', 'total_selling_price' => 32000.00, 'total_cost_price' => 24000.00, 'currency' => 'JPY'],
            ['status' => 'in_progress', 'type' => 'leisure',     'name' => 'Oman Road Trip',          'destination' => 'Muscat/Salalah, Oman',   'start_date' => '2026-07-05', 'end_date' => '2026-07-12', 'total_selling_price' => 7500.00,  'total_cost_price' => 5200.00,  'currency' => 'OMR'],
        ];

        $adminId = 1;

        foreach ($trips as $i => $data) {
            $num++;
            $data['trip_number'] = "T{$year}-" . str_pad($num, 4, '0', STR_PAD_LEFT);
            $data['customer_id'] = $customers[$i % count($customers)];
            $data['created_by'] = $adminId;
            Trip::create($data);
        }

        $this->command->info('Seeded ' . count($trips) . ' trips.');
    }
}
