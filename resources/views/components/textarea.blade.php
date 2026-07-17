@props(['label' => '', 'model' => '', 'rows' => 3, 'placeholder' => '', 'required' => false, 'help' => ''])

<div>
    @if ($label)
    <label for="{{ $model }}" class="block text-sm font-medium text-gray-700 mb-1">{{ $label }}</label>
    @endif
    <textarea
        id="{{ $model }}"
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes->merge(['class' => 'block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 ' . ($errors->has($model) ? 'border-red-300' : 'text-gray-900')]) }}
    ></textarea>
    @if ($help)<p class="mt-1 text-xs text-gray-500">{{ $help }}</p>@endif
    @error($model)<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>
