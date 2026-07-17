@props(['label' => '', 'name' => '', 'placeholder' => '', 'required' => false, 'disabled' => false, 'options' => [], 'help' => ''])

<div>
    @if ($label)
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-1 {{ $required ? 'after:content-[\'*\'] after:text-red-500 after:ml-0.5' : '' }}">{{ $label }}</label>
    @endif
    <select
        name="{{ $name }}"
        id="{{ $name }}"
        {{ $disabled ? 'disabled' : '' }}
        {{ $required ? 'required' : '' }}
        {{ $attributes->merge(['class' => 'block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 disabled:bg-gray-100 disabled:text-gray-500 ' . ($errors->has($name) ? 'border-red-300 text-red-900 focus:border-red-500 focus:ring-red-500' : 'text-gray-900')]) }}
    >
        @if ($placeholder)<option value="">{{ $placeholder }}</option>@endif
        @foreach($options as $value => $text)
            <option value="{{ $value }}" {{ $attributes->get('wire:model.live') && $attributes->get('wire:model.live') == $value ? 'selected' : '' }}>{{ $text }}</option>
        @endforeach
    </select>
    @if ($help)<p class="mt-1 text-xs text-gray-500">{{ $help }}</p>@endif
    @error($name)<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>
