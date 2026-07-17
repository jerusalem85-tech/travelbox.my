<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $this->migrateFlights();
        $this->migrateHotels();
        $this->migrateTransfers();
        $this->migrateVisas();
        $this->migrateInsurance();
        $this->migrateActivities();
        $this->migrateCruises();
        $this->migrateTrains();
        $this->migrateCars();
        $this->migratePackages();
        $this->migrateOthers();
    }

    public function down(): void
    {
        // Reversed migration would be complex; use fresh migrate instead
    }

    private function migrateFlights(): void
    {
        $rows = DB::table('flight_segments')->get();
        foreach ($rows as $r) {
            $detailId = DB::table('flight_details')->insertGetId([
                'airline'           => $r->airline,
                'flight_number'     => $r->flight_number,
                'departure_airport' => $r->departure_airport,
                'arrival_airport'   => $r->arrival_airport,
                'departure_terminal'=> $r->departure_terminal,
                'arrival_terminal'  => $r->arrival_terminal,
                'departure_datetime'=> $r->departure_datetime,
                'arrival_datetime'  => $r->arrival_datetime,
                'booking_reference' => $r->booking_reference,
                'ticket_number'     => $r->ticket_number,
                'class'             => $r->class,
                'cabin'             => $r->cabin,
                'fare_basis'        => $r->fare_basis,
                'baggage'           => $r->baggage,
                'seat'              => $r->seat,
                'meal'              => $r->meal,
            ]);
            DB::table('services')->insert([
                'id'                => Str::uuid(),
                'trip_id'           => $r->trip_id,
                'supplier_id'       => $r->supplier_id,
                'passenger_id'      => null,
                'type'              => 'flight',
                'name'              => trim($r->airline . ' ' . $r->flight_number),
                'status'            => $r->status ?? 'pending',
                'cost_price'        => $r->cost_price ?? 0,
                'selling_price'     => $r->selling_price ?? 0,
                'currency'          => $r->currency ?? 'USD',
                'notes'             => $r->notes,
                'detail_type'       => 'App\\Models\\FlightDetail',
                'detail_id'         => $detailId,
                'service_date'      => $r->departure_datetime ? date('Y-m-d', strtotime($r->departure_datetime)) : null,
                'service_end_date'  => $r->arrival_datetime ? date('Y-m-d', strtotime($r->arrival_datetime)) : null,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
        }
    }

    private function migrateHotels(): void
    {
        $rows = DB::table('hotel_bookings')->get();
        foreach ($rows as $r) {
            $detailId = DB::table('hotel_details')->insertGetId([
                'hotel_name'           => $r->hotel_name,
                'city'                 => $r->city,
                'address'              => $r->address,
                'check_in'             => $r->check_in,
                'check_out'            => $r->check_out,
                'check_in_time'        => $r->check_in_time,
                'check_out_time'       => $r->check_out_time,
                'room_type'            => $r->room_type,
                'meal_plan'            => $r->meal_plan,
                'number_of_rooms'      => $r->number_of_rooms ?? 1,
                'booking_reference'    => $r->booking_reference,
                'confirmation_number'  => $r->confirmation_number,
                'cancellation_policy'  => $r->cancellation_policy,
                'latitude'             => $r->latitude,
                'longitude'            => $r->longitude,
            ]);
            DB::table('services')->insert([
                'id'                => Str::uuid(),
                'trip_id'           => $r->trip_id,
                'supplier_id'       => $r->supplier_id,
                'passenger_id'      => null,
                'type'              => 'hotel',
                'name'              => $r->hotel_name,
                'status'            => $r->status ?? 'pending',
                'cost_price'        => $r->cost_price ?? 0,
                'selling_price'     => $r->selling_price ?? 0,
                'currency'          => $r->currency ?? 'USD',
                'notes'             => $r->notes,
                'detail_type'       => 'App\\Models\\HotelDetail',
                'detail_id'         => $detailId,
                'service_date'      => $r->check_in,
                'service_end_date'  => $r->check_out,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
        }
    }

    private function migrateTransfers(): void
    {
        $rows = DB::table('transfer_bookings')->get();
        foreach ($rows as $r) {
            $detailId = DB::table('transfer_details')->insertGetId([
                'type'                    => $r->type,
                'pickup_location'         => $r->pickup_location,
                'dropoff_location'        => $r->dropoff_location,
                'pickup_datetime'         => $r->pickup_datetime,
                'vehicle_type'            => $r->vehicle_type,
                'number_of_passengers'    => $r->number_of_passengers ?? 1,
                'booking_reference'       => $r->booking_reference,
            ]);
            DB::table('services')->insert([
                'id'                => Str::uuid(),
                'trip_id'           => $r->trip_id,
                'supplier_id'       => $r->supplier_id,
                'passenger_id'      => null,
                'type'              => 'transfer',
                'name'              => $r->pickup_location . ' → ' . $r->dropoff_location,
                'status'            => $r->status ?? 'pending',
                'cost_price'        => $r->cost_price ?? 0,
                'selling_price'     => $r->selling_price ?? 0,
                'currency'          => $r->currency ?? 'USD',
                'notes'             => $r->notes,
                'detail_type'       => 'App\\Models\\TransferDetail',
                'detail_id'         => $detailId,
                'service_date'      => $r->pickup_datetime ? date('Y-m-d', strtotime($r->pickup_datetime)) : null,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
        }
    }

    private function migrateVisas(): void
    {
        $rows = DB::table('visa_applications')->get();
        foreach ($rows as $r) {
            $detailId = DB::table('visa_details')->insertGetId([
                'country'                   => $r->country,
                'visa_type'                 => $r->visa_type,
                'application_date'          => $r->application_date,
                'expected_delivery_date'    => $r->expected_delivery_date,
                'actual_delivery_date'      => $r->actual_delivery_date,
            ]);
            DB::table('services')->insert([
                'id'                => Str::uuid(),
                'trip_id'           => $r->trip_id,
                'supplier_id'       => $r->supplier_id,
                'passenger_id'      => $r->passenger_id,
                'type'              => 'visa',
                'name'              => $r->country . ' ' . $r->visa_type,
                'status'            => $r->status ?? 'pending',
                'cost_price'        => $r->cost_price ?? 0,
                'selling_price'     => $r->selling_price ?? 0,
                'currency'          => $r->currency ?? 'USD',
                'notes'             => $r->notes,
                'detail_type'       => 'App\\Models\\VisaDetail',
                'detail_id'         => $detailId,
                'service_date'      => $r->application_date,
                'service_end_date'  => $r->expected_delivery_date,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
        }
    }

    private function migrateInsurance(): void
    {
        $rows = DB::table('insurance_policies')->get();
        foreach ($rows as $r) {
            $detailId = DB::table('insurance_details')->insertGetId([
                'policy_number'          => $r->policy_number,
                'type'                   => $r->type,
                'coverage_details'       => $r->coverage_details,
                'start_date'             => $r->start_date,
                'end_date'               => $r->end_date,
            ]);
            DB::table('services')->insert([
                'id'                => Str::uuid(),
                'trip_id'           => $r->trip_id,
                'supplier_id'       => $r->supplier_id,
                'passenger_id'      => $r->passenger_id,
                'type'              => 'insurance',
                'name'              => $r->type . ' Insurance',
                'status'            => $r->status ?? 'pending',
                'cost_price'        => $r->cost_price ?? 0,
                'selling_price'     => $r->selling_price ?? 0,
                'currency'          => $r->currency ?? 'USD',
                'notes'             => $r->notes,
                'detail_type'       => 'App\\Models\\InsuranceDetail',
                'detail_id'         => $detailId,
                'service_date'      => $r->start_date,
                'service_end_date'  => $r->end_date,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
        }
    }

    private function migrateActivities(): void
    {
        $rows = DB::table('activities')->get();
        foreach ($rows as $r) {
            $detailId = DB::table('activity_details')->insertGetId([
                'name'                      => $r->name,
                'type'                      => $r->type,
                'location'                  => $r->location,
                'date'                      => $r->date,
                'time'                      => $r->time,
                'duration'                  => $r->duration,
                'number_of_participants'    => $r->number_of_participants ?? 1,
                'booking_reference'         => $r->booking_reference,
            ]);
            DB::table('services')->insert([
                'id'                => Str::uuid(),
                'trip_id'           => $r->trip_id,
                'supplier_id'       => $r->supplier_id,
                'passenger_id'      => null,
                'type'              => 'activity',
                'name'              => $r->name,
                'status'            => $r->status ?? 'pending',
                'cost_price'        => $r->cost_price ?? 0,
                'selling_price'     => $r->selling_price ?? 0,
                'currency'          => $r->currency ?? 'USD',
                'notes'             => $r->notes,
                'detail_type'       => 'App\\Models\\ActivityDetail',
                'detail_id'         => $detailId,
                'service_date'      => $r->date,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
        }
    }

    private function migrateCruises(): void
    {
        $rows = DB::table('cruise_bookings')->get();
        foreach ($rows as $r) {
            $detailId = DB::table('cruise_details')->insertGetId([
                'cruise_line'       => $r->cruise_line,
                'ship_name'         => $r->ship_name,
                'cabin_type'        => $r->cabin_type,
                'cabin_number'      => $r->cabin_number,
                'departure_port'    => $r->departure_port,
                'arrival_port'      => $r->arrival_port,
                'departure_date'    => $r->departure_date,
                'arrival_date'      => $r->arrival_date,
                'itinerary'         => $r->itinerary,
                'booking_reference' => $r->booking_reference,
            ]);
            DB::table('services')->insert([
                'id'                => Str::uuid(),
                'trip_id'           => $r->trip_id,
                'supplier_id'       => $r->supplier_id,
                'passenger_id'      => null,
                'type'              => 'cruise',
                'name'              => $r->cruise_line . ' - ' . $r->ship_name,
                'status'            => $r->status ?? 'pending',
                'cost_price'        => $r->cost_price ?? 0,
                'selling_price'     => $r->selling_price ?? 0,
                'currency'          => $r->currency ?? 'USD',
                'notes'             => $r->notes,
                'detail_type'       => 'App\\Models\\CruiseDetail',
                'detail_id'         => $detailId,
                'service_date'      => $r->departure_date,
                'service_end_date'  => $r->arrival_date,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
        }
    }

    private function migrateTrains(): void
    {
        $rows = DB::table('train_bookings')->get();
        foreach ($rows as $r) {
            $detailId = DB::table('train_details')->insertGetId([
                'company'            => $r->company,
                'train_number'       => $r->train_number,
                'departure_station'  => $r->departure_station,
                'arrival_station'    => $r->arrival_station,
                'departure_datetime' => $r->departure_datetime,
                'arrival_datetime'   => $r->arrival_datetime,
                'class'              => $r->class,
                'carriage'           => $r->carriage,
                'seat'               => $r->seat,
                'booking_reference'  => $r->booking_reference,
                'ticket_type'        => $r->ticket_type,
            ]);
            DB::table('services')->insert([
                'id'                => Str::uuid(),
                'trip_id'           => $r->trip_id,
                'supplier_id'       => $r->supplier_id,
                'passenger_id'      => null,
                'type'              => 'train',
                'name'              => trim($r->company . ' ' . $r->train_number),
                'status'            => $r->status ?? 'pending',
                'cost_price'        => $r->cost_price ?? 0,
                'selling_price'     => $r->selling_price ?? 0,
                'currency'          => $r->currency ?? 'USD',
                'notes'             => $r->notes,
                'detail_type'       => 'App\\Models\\TrainDetail',
                'detail_id'         => $detailId,
                'service_date'      => $r->departure_datetime ? date('Y-m-d', strtotime($r->departure_datetime)) : null,
                'service_end_date'  => $r->arrival_datetime ? date('Y-m-d', strtotime($r->arrival_datetime)) : null,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
        }
    }

    private function migrateCars(): void
    {
        $rows = DB::table('car_rentals')->get();
        foreach ($rows as $r) {
            $detailId = DB::table('car_details')->insertGetId([
                'company'            => $r->company,
                'car_type'           => $r->car_type,
                'car_model'          => $r->car_model,
                'pickup_location'    => $r->pickup_location,
                'dropoff_location'   => $r->dropoff_location,
                'pickup_datetime'    => $r->pickup_datetime,
                'dropoff_datetime'   => $r->dropoff_datetime,
                'booking_reference'  => $r->booking_reference,
                'license_plate'      => $r->license_plate,
                'include_insurance'  => $r->include_insurance ?? false,
                'included_km'        => $r->included_km,
                'daily_limit_km'     => $r->daily_limit_km,
            ]);
            DB::table('services')->insert([
                'id'                => Str::uuid(),
                'trip_id'           => $r->trip_id,
                'supplier_id'       => $r->supplier_id,
                'passenger_id'      => null,
                'type'              => 'car',
                'name'              => trim($r->company . ' ' . $r->car_type),
                'status'            => $r->status ?? 'pending',
                'cost_price'        => $r->cost_price ?? 0,
                'selling_price'     => $r->selling_price ?? 0,
                'currency'          => $r->currency ?? 'USD',
                'notes'             => $r->notes,
                'detail_type'       => 'App\\Models\\CarDetail',
                'detail_id'         => $detailId,
                'service_date'      => $r->pickup_datetime ? date('Y-m-d', strtotime($r->pickup_datetime)) : null,
                'service_end_date'  => $r->dropoff_datetime ? date('Y-m-d', strtotime($r->dropoff_datetime)) : null,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
        }
    }

    private function migratePackages(): void
    {
        $rows = DB::table('package_bookings')->get();
        foreach ($rows as $r) {
            $detailId = DB::table('package_details')->insertGetId([
                'name'               => $r->name,
                'type'               => $r->type,
                'description'        => $r->description,
                'start_date'         => $r->start_date,
                'end_date'           => $r->end_date,
                'destination'        => $r->destination,
                'number_of_nights'   => $r->number_of_nights,
                'number_of_rooms'    => $r->number_of_rooms ?? 1,
                'room_type'          => $r->room_type,
                'meal_plan'          => $r->meal_plan,
                'booking_reference'  => $r->booking_reference,
                'inclusions'         => $r->inclusions,
                'exclusions'         => $r->exclusions,
            ]);
            DB::table('services')->insert([
                'id'                => Str::uuid(),
                'trip_id'           => $r->trip_id,
                'supplier_id'       => $r->supplier_id,
                'passenger_id'      => null,
                'type'              => 'package',
                'name'              => $r->name,
                'status'            => $r->status ?? 'pending',
                'cost_price'        => $r->cost_price ?? 0,
                'selling_price'     => $r->selling_price ?? 0,
                'currency'          => $r->currency ?? 'USD',
                'notes'             => $r->notes,
                'detail_type'       => 'App\\Models\\PackageDetail',
                'detail_id'         => $detailId,
                'service_date'      => $r->start_date,
                'service_end_date'  => $r->end_date,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
        }
    }

    private function migrateOthers(): void
    {
        $rows = DB::table('other_services')->get();
        foreach ($rows as $r) {
            $detailId = DB::table('other_details')->insertGetId([
                'name'               => $r->name,
                'category'           => $r->category,
                'description'        => $r->description,
                'service_date'       => $r->service_date,
                'location'           => $r->location,
                'booking_reference'  => $r->booking_reference,
                'quantity'           => $r->quantity ?? 1,
            ]);
            DB::table('services')->insert([
                'id'                => Str::uuid(),
                'trip_id'           => $r->trip_id,
                'supplier_id'       => $r->supplier_id,
                'passenger_id'      => null,
                'type'              => 'other',
                'name'              => $r->name,
                'status'            => $r->status ?? 'pending',
                'cost_price'        => $r->cost_price ?? 0,
                'selling_price'     => $r->selling_price ?? 0,
                'currency'          => $r->currency ?? 'USD',
                'notes'             => $r->notes,
                'detail_type'       => 'App\\Models\\OtherDetail',
                'detail_id'         => $detailId,
                'service_date'      => $r->service_date,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
        }
    }
};
