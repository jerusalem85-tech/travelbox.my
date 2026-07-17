<x-app-layout>
    <x-slot name="header">Trips</x-slot>
    <x-admin-placeholder
        title="Trip Management"
        description="Create and manage travel trips including flights, hotels, transfers, and all trip-related services."
        :route="route('trips.create')" />
</x-app-layout>
