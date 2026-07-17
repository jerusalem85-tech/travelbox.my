@props(['title', 'description', 'route' => null])
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
        </svg>
    </div>
    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $title }}</h3>
    <p class="text-gray-500 mb-6 max-w-md mx-auto">{{ $description }}</p>
    @if ($route)
        <a href="{{ $route }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
            Create {{ str($title)->before(' ')->lower() }}
        </a>
    @endif
</div>
