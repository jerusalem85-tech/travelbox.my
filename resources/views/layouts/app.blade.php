<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'TravelBox ERP') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-50" x-data="{ sidebarOpen: true }" x-init="sidebarOpen = localStorage.getItem('sidebarState') !== null ? localStorage.getItem('sidebarState') === 'true' : true">
        <x-toast />
        <livewire:layout.navigation />

        <div class="flex h-full">
            @auth
            {{-- Overlay backdrop (mobile only) --}}
            <div x-show="sidebarOpen" x-cloak class="fixed inset-0 z-40 bg-black/40 lg:hidden" @click="sidebarOpen = false"></div>

            {{-- Sidebar --}}
            <aside :class="sidebarOpen ? 'w-72' : 'w-0 lg:w-16'" class="flex-shrink-0 bg-white border-r border-gray-200 overflow-hidden transition-all duration-200">
                <div class="w-72 h-full overflow-y-auto">
                <div class="flex items-center justify-between px-4 h-16 border-b border-gray-200">
                    <span class="text-sm font-bold text-gray-900" x-show="sidebarOpen">Navigation</span>
                    <span class="text-sm font-bold text-gray-900" x-show="!sidebarOpen" class="lg:hidden">N</span>
                    <button @click="sidebarOpen = !sidebarOpen" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <nav class="p-4 space-y-1">
                    <div class="flex items-center gap-3 px-3 py-2.5 mb-2 border-b border-gray-100">
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">TB</div>
                        <div x-show="sidebarOpen">
                            <p class="text-sm font-semibold text-gray-900">TravelBox</p>
                            <p class="text-xs text-gray-400">ERP System</p>
                        </div>
                    </div>

                    <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        Dashboard
                    </a>

                    <div class="pt-3">
                        <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Trips</p>
                        <a href="{{ route('trips.index') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 mt-1 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('trips.index') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                            All Trips
                        </a>
                        <a href="{{ route('trips.calendar') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('trips.calendar') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Calendar
                        </a>
                        <a href="{{ route('trips.pipeline') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('trips.pipeline') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                            Pipeline
                        </a>
                    </div>

                    <div class="pt-3">
                        <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Contacts</p>
                        <a href="{{ route('customers.index') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 mt-1 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('customers.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            Customers
                        </a>
                        <a href="{{ route('suppliers.index') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('suppliers.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            Suppliers
                        </a>
                    </div>

                    <div class="pt-3">
                        <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Finance</p>
                        <a href="{{ route('invoices.index') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 mt-1 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('invoices.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Invoices
                        </a>
                        <a href="{{ route('payments.index') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('payments.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            Payments
                        </a>
                        <div x-data="{ open: window.location.href.includes('/accounting/') }">
                            <button @click="open = !open" class="flex items-center justify-between w-full px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('accounting.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                                <span class="flex items-center gap-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                    Accounting
                                </span>
                                <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" x-cloak class="mt-1 space-y-0.5 ml-3">
                                <a href="{{ route('accounting.general-ledger') }}" wire:navigate class="flex items-center gap-3 px-3 py-1.5 pl-6 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('accounting.general-ledger') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}">General Ledger</a>
                                <a href="{{ route('accounting.trial-balance') }}" wire:navigate class="flex items-center gap-3 px-3 py-1.5 pl-6 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('accounting.trial-balance') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}">Trial Balance</a>
                                <a href="{{ route('accounting.profit-loss') }}" wire:navigate class="flex items-center gap-3 px-3 py-1.5 pl-6 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('accounting.profit-loss') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}">Profit & Loss</a>
                                <a href="{{ route('accounting.balance-sheet') }}" wire:navigate class="flex items-center gap-3 px-3 py-1.5 pl-6 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('accounting.balance-sheet') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}">Balance Sheet</a>
                                <a href="{{ route('accounting.cash-flow') }}" wire:navigate class="flex items-center gap-3 px-3 py-1.5 pl-6 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('accounting.cash-flow') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}">Cash Flow</a>
                            </div>
                        </div>
                        <a href="{{ route('expenses.index') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('expenses.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Expenses
                        </a>
                    </div>

                    <div class="pt-3">
                        <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Reports</p>
                        <a href="{{ route('reports.sales') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 mt-1 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('reports.sales') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            Sales Report
                        </a>
                        <a href="{{ route('reports.profit') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('reports.profit') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                            Profit Report
                        </a>
                        <a href="#" class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg text-gray-400 cursor-not-allowed" title="Coming soon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Commission Report
                            <span class="ml-auto text-[10px] text-gray-300 bg-gray-100 px-1.5 py-0.5 rounded">Soon</span>
                        </a>
                        <a href="{{ route('reports.customer-aging') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('reports.customer-aging') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            Customer Aging
                        </a>
                        <a href="{{ route('reports.supplier-aging') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('reports.supplier-aging') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            Supplier Aging
                        </a>
                        <a href="{{ route('reports.tax-summary') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('reports.tax-summary') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                            Tax Summary
                        </a>
                    </div>

                    @can('settings.manage')
                    <div class="pt-3">
                        <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">System</p>
                        <a href="{{ route('settings.index') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 mt-1 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('settings.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Settings
                        </a>
                    </div>
                    @endcan
                </nav>
                </div>
            </aside>
            @endauth

            <main :class="sidebarOpen ? 'lg:ml-0' : ''" class="flex-1 min-w-0 p-4 lg:p-6">
                @if (isset($header))
                <header class="mb-6">
                    <h2 class="text-xl lg:text-2xl font-bold text-gray-900">{{ $header }}</h2>
                </header>
                @endif
                {{ $slot }}
            </main>
        </div>
    </div>
    <style>[x-cloak]{display:none!important}</style>
    @stack('styles')
    @livewireScripts
    @stack('scripts')
</body>
</html>