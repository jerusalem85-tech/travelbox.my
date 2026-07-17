<?php

namespace Database\Seeders;

use App\Models\Trip;
use App\Models\Passenger;
use Illuminate\Database\Seeder;

class PassengerSeeder extends Seeder
{
    public function run(): void
    {
        $trips = Trip::all();
        if ($trips->isEmpty()) {
            $this->command->error('No trips found. Run TripSeeder first.');
            return;
        }

        $firstNames = ['Ahmed', 'Fatima', 'Mohamed', 'Aisha', 'Omar', 'Layla', 'Ali', 'Sara', 'Hassan', 'Noor',
                        'Youssef', 'Mariam', 'Ibrahim', 'Zainab', 'Khalid', 'Huda', 'Saeed', 'Amira', 'Tariq', 'Rania',
                        'Faisal', 'Nadia', 'Sami', 'Lina'];
        $lastNames  = ['Al-Rashid', 'Al-Hassan', 'Al-Mansouri', 'Al-Salem', 'Al-Otaibi', 'Al-Zahrani', 'Al-Ansari',
                        'Al-Qahtani', 'Al-Dosari', 'Al-Shareef', 'Al-Sulaiti', 'Al-Naimi', 'Al-Kuwari', 'Al-Baker',
                        'Al-Marri', 'Al-Hajri', 'Al-Mohannadi', 'Al-Subaie', 'Al-Abdulmalik', 'Al-Thani',
                        'Al-Mutairi', 'Al-Malik', 'Al-Ghanim', 'Al-Fahad'];
        $nationalities = ['UAE', 'Saudi', 'Qatari', 'Omani', 'Bahraini', 'Kuwaiti', 'Egyptian', 'Jordanian',
                          'Lebanese', 'Moroccan', 'Algerian', 'Tunisian', 'Syrian', 'Iraqi', 'Yemeni', 'Palestinian',
                          'Libyan', 'Sudanese', 'Somali', 'Mauritanian', 'Comorian', 'Djiboutian', 'Turkish', 'Pakistani'];

        for ($i = 0; $i < 24; $i++) {
            $trip = $trips[$i % $trips->count()];
            Passenger::create([
                'trip_id'    => $trip->id,
                'first_name' => $firstNames[$i],
                'last_name'  => $lastNames[$i],
                'nationality' => $nationalities[$i],
                'date_of_birth' => now()->subYears(rand(20, 60))->subDays(rand(0, 365)),
                'passport_number' => 'N' . str_pad((string) rand(1, 999999), 6, '0', STR_PAD_LEFT),
                'passport_expiry' => now()->addYears(rand(1, 10)),
            ]);
        }

        $this->command->info("Seeded 24 passengers across {$trips->count()} trips.");
    }
}
