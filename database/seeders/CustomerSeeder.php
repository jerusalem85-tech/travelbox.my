<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            ['type' => 'individual', 'first_name' => 'Ahmed', 'last_name' => 'Al Mansouri', 'email' => 'ahmed@example.ae', 'phone' => '+971 50 111 2233', 'city' => 'Dubai', 'country' => 'UAE', 'nationality' => 'Emirati', 'preferred_currency' => 'AED'],
            ['type' => 'individual', 'first_name' => 'Fatima', 'last_name' => 'Al Zahrani', 'email' => 'fatima@example.ae', 'phone' => '+971 55 222 3344', 'city' => 'Abu Dhabi', 'country' => 'UAE', 'nationality' => 'Emirati', 'preferred_currency' => 'AED'],
            ['type' => 'corporate', 'first_name' => 'Mohammed', 'last_name' => 'Al Shamsi', 'company_name' => 'Al Shamsi Group LLC', 'email' => 'info@alshamsigroup.ae', 'phone' => '+971 4 333 4455', 'city' => 'Dubai', 'country' => 'UAE', 'nationality' => 'Emirati', 'preferred_currency' => 'AED'],
            ['type' => 'individual', 'first_name' => 'Layla', 'last_name' => 'Hassan', 'email' => 'layla.h@example.com', 'phone' => '+971 56 444 5566', 'city' => 'Sharjah', 'country' => 'UAE', 'nationality' => 'Egyptian', 'preferred_currency' => 'USD'],
            ['type' => 'corporate', 'first_name' => 'Omar', 'last_name' => 'Al Futtaim', 'company_name' => 'Al Futtaim Travel', 'email' => 'corporate@alfuttaim.ae', 'phone' => '+971 4 555 6677', 'city' => 'Dubai', 'country' => 'UAE', 'nationality' => 'Emirati', 'preferred_currency' => 'AED'],
            ['type' => 'individual', 'first_name' => 'Sarah', 'last_name' => 'Johnson', 'email' => 'sarah.j@example.com', 'phone' => '+44 77 8888 9900', 'city' => 'London', 'country' => 'UK', 'nationality' => 'British', 'preferred_currency' => 'GBP'],
            ['type' => 'individual', 'first_name' => 'Khalid', 'last_name' => 'Al Qahtani', 'email' => 'khalid@example.sa', 'phone' => '+966 55 666 7788', 'city' => 'Riyadh', 'country' => 'Saudi Arabia', 'nationality' => 'Saudi', 'preferred_currency' => 'SAR'],
            ['type' => 'corporate', 'first_name' => 'Noor', 'last_name' => 'Al Hashemi', 'company_name' => 'Noor Travel Agency', 'email' => 'info@noortravel.ae', 'phone' => '+971 4 777 8899', 'city' => 'Dubai', 'country' => 'UAE', 'nationality' => 'Emirati', 'preferred_currency' => 'AED'],
            ['type' => 'individual', 'first_name' => 'David', 'last_name' => 'Chen', 'email' => 'david.chen@example.com', 'phone' => '+1 415 555 9900', 'city' => 'San Francisco', 'country' => 'USA', 'nationality' => 'American', 'preferred_currency' => 'USD'],
            ['type' => 'individual', 'first_name' => 'Aisha', 'last_name' => 'Al Balushi', 'email' => 'aisha@example.om', 'phone' => '+968 99 111 2233', 'city' => 'Muscat', 'country' => 'Oman', 'nationality' => 'Omani', 'preferred_currency' => 'OMR'],
        ];

        $prefix = 'CUS';
        $last = Customer::withTrashed()->max('customer_code');
        $num = $last ? (int) substr($last, 4) : 0;

        $adminId = 1;

        foreach ($customers as $data) {
            $num++;
            $data['customer_code'] = $prefix . '-' . str_pad($num, 4, '0', STR_PAD_LEFT);
            $data['is_active'] = true;
            $data['current_balance'] = 0;
            $data['credit_limit'] = $data['type'] === 'corporate' ? 50000 : 10000;
            $data['created_by'] = $adminId;
            Customer::create($data);
        }

        $this->command->info('Seeded ' . count($customers) . ' customers.');
    }
}
