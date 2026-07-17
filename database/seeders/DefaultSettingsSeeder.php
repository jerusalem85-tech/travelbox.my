<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class DefaultSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'company_name', 'value' => 'TravelBox Travel Agency'],
            ['key' => 'company_email', 'value' => 'info@travelbox.my'],
            ['key' => 'company_phone', 'value' => ''],
            ['key' => 'company_address', 'value' => ''],
            ['key' => 'default_currency', 'value' => 'USD'],
            ['key' => 'tax_rate', 'value' => '0'],
            ['key' => 'trip_number_prefix', 'value' => 'T-'],
            ['key' => 'invoice_number_prefix', 'value' => 'INV-'],
            ['key' => 'payment_number_prefix', 'value' => 'PAY-'],
            ['key' => 'customer_code_prefix', 'value' => 'CUST-'],
            ['key' => 'supplier_code_prefix', 'value' => 'SUPP-'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
