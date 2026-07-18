<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<nav x-data="{ open: false }" class="bg-white border-b border-gray-200 sticky top-0 z-30">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <div class="flex items-center gap-6">
                <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-2.5 shrink-0">
                    <x-application-logo class="block h-8 w-auto fill-current text-gray-800" />
                    <span class="hidden sm:inline text-sm font-bold text-gray-800">TravelBox</span>
                </a>

                <div class="hidden lg:flex items-center gap-1">
                    <a href="{{ route('trips.index') }}" wire:navigate class="px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('trips.*') ? 'text-blue-700 bg-blue-50' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">Trips</a>
                    <a href="{{ route('customers.index') }}" wire:navigate class="px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('customers.*') ? 'text-blue-700 bg-blue-50' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">Customers</a>
                    <a href="{{ route('suppliers.index') }}" wire:navigate class="px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('suppliers.*') ? 'text-blue-700 bg-blue-50' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">Suppliers</a>
                    <a href="{{ route('invoices.index') }}" wire:navigate class="px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('invoices.*') ? 'text-blue-700 bg-blue-50' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">Invoices</a>
                    <a href="{{ route('payments.index') }}" wire:navigate class="px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('payments.*') ? 'text-blue-700 bg-blue-50' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">Payments</a>
                    <a href="{{ route('expenses.index') }}" wire:navigate class="px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('expenses.*') ? 'text-blue-700 bg-blue-50' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">Expenses</a>
                    <a href="{{ route('accounting.chart-of-accounts') }}" wire:navigate class="px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('accounting.*') ? 'text-blue-700 bg-blue-50' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">Accounting</a>
                    <a href="{{ route('reports.sales') }}" wire:navigate class="px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('reports.*') ? 'text-blue-700 bg-blue-50' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">Reports</a>
                    <a href="{{ route('settings.index') }}" wire:navigate class="px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('settings.*') ? 'text-blue-700 bg-blue-50' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">Settings</a>
                </div>
            </div>

            <div class="flex items-center gap-2">
                @livewire('global-search')
                @auth
                @livewire('notification-bell')
                @endauth

                <div class="hidden sm:flex sm:items-center">
                    @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition-colors">
                                <div class="w-7 h-7 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-xs font-bold">
                                    {{ substr(auth()->user()->name, 0, 2) }}
                                </div>
                                <span class="hidden lg:inline" x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link href="/profile" wire:navigate>Profile</x-dropdown-link>
                            <button wire:click="logout" class="w-full text-start">
                                <x-dropdown-link>Log Out</x-dropdown-link>
                            </button>
                        </x-slot>
                    </x-dropdown>
                    @else
                    <a href="/login" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">Log in</a>
                    <a href="/register" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 ml-2">Register</a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</nav>
