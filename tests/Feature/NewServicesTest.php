<?php

namespace Tests\Feature;

use App\Models\Trip;
use App\Models\Customer;
use App\Models\CruiseBooking;
use App\Models\TrainBooking;
use App\Models\CarRental;
use App\Models\PackageBooking;
use App\Models\OtherService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class NewServicesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->create());
    }

    private function makeTrip(): Trip
    {
        $customer = Customer::factory()->create();
        return Trip::create([
            'trip_number' => 'T2026-0001',
            'customer_id' => $customer->id,
            'status' => 'confirmed',
            'type' => 'leisure',
            'name' => 'Test Trip',
            'destination' => 'Bahamas',
            'start_date' => now(),
            'end_date' => now()->addDays(7),
            'currency' => 'USD',
            'created_by' => auth()->id(),
        ]);
    }

    public function test_cruise_booking_can_be_created(): void
    {
        $trip = $this->makeTrip();

        Livewire::test(\App\Livewire\Trips\TripShow::class, ['trip' => $trip])
            ->set('cr_cruise_line', 'Royal Caribbean')
            ->set('cr_ship_name', 'Symphony')
            ->set('cr_departure_port', 'Miami')
            ->set('cr_arrival_port', 'Nassau')
            ->set('cr_departure_date', now()->format('Y-m-d'))
            ->set('cr_selling_price', 1500)
            ->set('cr_cost_price', 1200)
            ->call('saveCruise');

        $this->assertDatabaseHas('cruise_bookings', [
            'trip_id' => $trip->id,
            'cruise_line' => 'Royal Caribbean',
            'ship_name' => 'Symphony',
        ]);

        $trip->refresh();
        $this->assertEquals(1500, $trip->total_selling_price);
        $this->assertEquals(1200, $trip->total_cost_price);
    }

    public function test_train_booking_can_be_created(): void
    {
        $trip = $this->makeTrip();

        Livewire::test(\App\Livewire\Trips\TripShow::class, ['trip' => $trip])
            ->set('tr_company', 'Eurostar')
            ->set('tr_train_number', '9024')
            ->set('tr_departure_station', 'London')
            ->set('tr_arrival_station', 'Paris')
            ->set('tr_departure_datetime', now()->format('Y-m-d H:i:s'))
            ->set('tr_selling_price', 200)
            ->set('tr_cost_price', 150)
            ->call('saveTrain');

        $this->assertDatabaseHas('train_bookings', [
            'trip_id' => $trip->id,
            'train_company' => 'Eurostar',
        ]);
    }

    public function test_car_rental_can_be_created(): void
    {
        $trip = $this->makeTrip();

        Livewire::test(\App\Livewire\Trips\TripShow::class, ['trip' => $trip])
            ->set('ca_company', 'Hertz')
            ->set('ca_car_type', 'SUV')
            ->set('ca_pickup_location', 'Airport')
            ->set('ca_dropoff_location', 'Hotel')
            ->set('ca_pickup_datetime', now()->format('Y-m-d H:i:s'))
            ->set('ca_selling_price', 300)
            ->set('ca_cost_price', 250)
            ->call('saveCar');

        $this->assertDatabaseHas('car_rentals', [
            'trip_id' => $trip->id,
            'company' => 'Hertz',
        ]);
    }

    public function test_package_booking_can_be_created(): void
    {
        $trip = $this->makeTrip();

        Livewire::test(\App\Livewire\Trips\TripShow::class, ['trip' => $trip])
            ->set('pk_name', 'Honeymoon Package')
            ->set('pk_type', 'all_inclusive')
            ->set('pk_start_date', now()->format('Y-m-d'))
            ->set('pk_end_date', now()->addDays(7)->format('Y-m-d'))
            ->set('pk_selling_price', 5000)
            ->set('pk_cost_price', 4000)
            ->call('savePackage');

        $this->assertDatabaseHas('package_bookings', [
            'trip_id' => $trip->id,
            'package_name' => 'Honeymoon Package',
        ]);
    }

    public function test_other_service_can_be_created(): void
    {
        $trip = $this->makeTrip();

        Livewire::test(\App\Livewire\Trips\TripShow::class, ['trip' => $trip])
            ->set('o_name', 'Airport Lounge')
            ->set('o_type', 'lounge')
            ->set('o_date', now()->format('Y-m-d'))
            ->set('o_selling_price', 100)
            ->set('o_cost_price', 80)
            ->call('saveOther');

        $this->assertDatabaseHas('other_services', [
            'trip_id' => $trip->id,
            'service_name' => 'Airport Lounge',
        ]);
    }

    public function test_cruise_booking_can_be_deleted_and_totals_recalc(): void
    {
        $trip = $this->makeTrip();
        CruiseBooking::create([
            'trip_id' => $trip->id,
            'cruise_line' => 'Royal Caribbean',
            'selling_price' => 1500,
            'cost_price' => 1200,
            'currency' => 'USD',
            'status' => 'confirmed',
            'departure_port' => 'Miami',
            'arrival_port' => 'Nassau',
        ]);
        $trip->recalculateTotals();
        $trip->refresh();
        $this->assertEquals(1500, $trip->total_selling_price);

        $booking = $trip->cruiseBookings()->first();

        Livewire::test(\App\Livewire\Trips\TripShow::class, ['trip' => $trip])
            ->call('deleteCruise', $booking->id);

        $this->assertSoftDeleted('cruise_bookings', ['id' => $booking->id]);
        $trip->refresh();
        $this->assertEquals(0, $trip->total_selling_price);
    }

    public function test_all_service_models_belong_to_trip(): void
    {
        $trip = $this->makeTrip();

        $cruise = CruiseBooking::create(['trip_id' => $trip->id, 'cruise_line' => 'C', 'selling_price' => 1, 'cost_price' => 1, 'currency' => 'USD', 'status' => 'confirmed', 'departure_port' => 'A', 'arrival_port' => 'B']);
        $train = TrainBooking::create(['trip_id' => $trip->id, 'train_company' => 'T', 'selling_price' => 1, 'cost_price' => 1, 'currency' => 'USD', 'status' => 'confirmed', 'departure_station' => 'A', 'arrival_station' => 'B', 'departure_datetime' => now()]);
        $car = CarRental::create(['trip_id' => $trip->id, 'company' => 'H', 'selling_price' => 1, 'cost_price' => 1, 'currency' => 'USD', 'status' => 'confirmed', 'pickup_location' => 'A', 'dropoff_location' => 'B', 'pickup_datetime' => now()]);
        $package = PackageBooking::create(['trip_id' => $trip->id, 'package_name' => 'P', 'selling_price' => 1, 'cost_price' => 1, 'currency' => 'USD', 'status' => 'confirmed', 'start_date' => now(), 'end_date' => now()]);
        $other = OtherService::create(['trip_id' => $trip->id, 'service_name' => 'O', 'selling_price' => 1, 'cost_price' => 1, 'currency' => 'USD', 'status' => 'confirmed', 'service_date' => now()]);

        $this->assertNotNull($cruise->trip);
        $this->assertNotNull($train->trip);
        $this->assertNotNull($car->trip);
        $this->assertNotNull($package->trip);
        $this->assertNotNull($other->trip);

        $this->assertTrue($trip->cruiseBookings->contains($cruise));
        $this->assertTrue($trip->trainBookings->contains($train));
        $this->assertTrue($trip->carRentals->contains($car));
        $this->assertTrue($trip->packageBookings->contains($package));
        $this->assertTrue($trip->otherServices->contains($other));
    }
}
