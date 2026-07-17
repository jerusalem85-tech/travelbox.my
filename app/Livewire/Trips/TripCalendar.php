<?php

namespace App\Livewire\Trips;

use App\Models\Trip;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Carbon\Carbon;

#[Layout('layouts.app')]
class TripCalendar extends Component
{
    public int $month;
    public int $year;
    public ?string $expandedDate = null;

    public function toggleExpand(string $date): void
    {
        $this->expandedDate = $this->expandedDate === $date ? null : $date;
    }

    public function mount(): void
    {
        $this->month = now()->month;
        $this->year = now()->year;
    }

    public function previousMonth(): void
    {
        $this->month--;
        if ($this->month < 1) {
            $this->month = 12;
            $this->year--;
        }
    }

    public function nextMonth(): void
    {
        $this->month++;
        if ($this->month > 12) {
            $this->month = 1;
            $this->year++;
        }
    }

    public function goToToday(): void
    {
        $this->month = now()->month;
        $this->year = now()->year;
    }

    public function render()
    {
        $start = Carbon::create($this->year, $this->month, 1);
        $end = $start->copy()->endOfMonth();

        $cursor = $start->copy()->startOfWeek(Carbon::SUNDAY);
        $endOfGrid = $end->copy()->endOfWeek(Carbon::SATURDAY);

        $trips = Trip::where(function ($q) use ($start, $end) {
                $q->whereBetween('start_date', [$start, $end])
                  ->orWhereBetween('end_date', [$start, $end])
                  ->orWhere(function ($q) use ($start, $end) {
                      $q->where('start_date', '<=', $start)
                        ->where('end_date', '>=', $end);
                  });
            })
            ->orderBy('start_date')
            ->get(['id', 'trip_number', 'name', 'status', 'start_date', 'end_date']);

        $tripMap = [];
        foreach ($trips as $trip) {
            $tStart = $trip->start_date instanceof Carbon ? $trip->start_date : Carbon::parse($trip->start_date);
            $tEnd = $trip->end_date instanceof Carbon ? $trip->end_date : Carbon::parse($trip->end_date);
            $day = $tStart->copy();
            while ($day->lte($tEnd)) {
                $key = $day->format('Y-m-d');
                $tripMap[$key][] = [
                    'id' => $trip->id,
                    'name' => $trip->name ?? $trip->trip_number,
                    'trip_number' => $trip->trip_number,
                    'status' => $trip->status,
                ];
                $day->addDay();
            }
        }

        $weeks = [];
        while ($cursor->lte($endOfGrid)) {
            $week = [];
            for ($i = 0; $i < 7; $i++) {
                $dateStr = $cursor->format('Y-m-d');
                $dayTrips = $tripMap[$dateStr] ?? [];
                $week[] = [
                    'day' => $cursor->day,
                    'date' => $dateStr,
                    'isCurrentMonth' => $cursor->month === $this->month,
                    'isToday' => $cursor->isToday(),
                    'trips' => $dayTrips,
                    'tripCount' => count($dayTrips),
                ];
                $cursor->addDay();
            }
            $weeks[] = $week;
        }

        return view('livewire.trips.trip-calendar', [
            'monthName' => $start->format('F Y'),
            'weeks' => $weeks,
        ]);
    }
}
