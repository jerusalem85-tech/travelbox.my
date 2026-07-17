<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            ['type' => 'airline', 'company_name' => 'Emirates', 'contact_person' => 'Ahmed Al Maktoum', 'email' => 'reservations@emirates.ae', 'phone' => '+971 4 555 1234', 'city' => 'Dubai', 'country' => 'UAE', 'preferred_currency' => 'AED'],
            ['type' => 'hotel', 'company_name' => 'Marriott Hotels Dubai', 'contact_person' => 'Sara Khan', 'email' => 'groups@marriott.ae', 'phone' => '+971 4 555 5678', 'city' => 'Dubai', 'country' => 'UAE', 'preferred_currency' => 'AED'],
            ['type' => 'airline', 'company_name' => 'Qatar Airways', 'contact_person' => 'Fahad Al Thani', 'email' => 'corporate@qatarairways.qa', 'phone' => '+974 555 1234', 'city' => 'Doha', 'country' => 'Qatar', 'preferred_currency' => 'QAR'],
            ['type' => 'hotel', 'company_name' => 'Hilton Dubai Palm', 'contact_person' => 'John Smith', 'email' => 'reservations@hilton.ae', 'phone' => '+971 4 555 9012', 'city' => 'Dubai', 'country' => 'UAE', 'preferred_currency' => 'AED'],
            ['type' => 'transfer', 'company_name' => 'Hertz Rent a Car', 'contact_person' => 'Mahmoud Abbas', 'email' => 'reservations@hertz.ae', 'phone' => '+971 4 555 3456', 'city' => 'Dubai', 'country' => 'UAE', 'preferred_currency' => 'AED'],
            ['type' => 'airline', 'company_name' => 'Saudia Airlines', 'contact_person' => 'Khalid Al Otaibi', 'email' => 'groups@saudia.com', 'phone' => '+966 12 555 7890', 'city' => 'Jeddah', 'country' => 'Saudi Arabia', 'preferred_currency' => 'SAR'],
            ['type' => 'visa', 'company_name' => 'VFS Global UAE', 'contact_person' => 'Priya Sharma', 'email' => 'info@vfsglobal.ae', 'phone' => '+971 4 555 2345', 'city' => 'Dubai', 'country' => 'UAE', 'preferred_currency' => 'AED'],
            ['type' => 'insurance', 'company_name' => 'Allianz Travel Insurance', 'contact_person' => 'Mark Wilson', 'email' => 'claims@allianz.ae', 'phone' => '+971 4 555 6789', 'city' => 'Dubai', 'country' => 'UAE', 'preferred_currency' => 'USD'],
            ['type' => 'tour_operator', 'company_name' => 'Arabian Adventures', 'contact_person' => 'Layla Hassan', 'email' => 'bookings@arabianadventures.ae', 'phone' => '+971 4 555 4567', 'city' => 'Dubai', 'country' => 'UAE', 'preferred_currency' => 'AED'],
            ['type' => 'transfer', 'company_name' => 'Uber Business UAE', 'contact_person' => 'Omar Rashid', 'email' => 'business@uber.ae', 'phone' => '+971 4 555 8901', 'city' => 'Dubai', 'country' => 'UAE', 'preferred_currency' => 'AED'],
        ];

        $prefixes = [
            'airline' => 'AIR', 'hotel' => 'HTL', 'transfer' => 'TRF',
            'visa' => 'VIS', 'insurance' => 'INS', 'tour_operator' => 'TOP',
        ];

        $counters = [];
        $adminId = \App\Models\User::where('email', 'admin@travelbox.my')->value('id');

        foreach ($suppliers as $data) {
            $type = $data['type'];
            if (!isset($counters[$type])) {
                $last = Supplier::where('type', $type)->withTrashed()->max('supplier_code');
                $counters[$type] = $last ? (int) substr($last, -4) : 0;
            }
            $counters[$type]++;
            $data['supplier_code'] = $prefixes[$type] . '-' . str_pad($counters[$type], 4, '0', STR_PAD_LEFT);
            $data['is_active'] = true;
            $data['current_balance'] = 0;
            $data['created_by'] = $adminId;
            Supplier::create($data);
        }

        $this->command->info('Seeded ' . count($suppliers) . ' suppliers.');
    }
}
