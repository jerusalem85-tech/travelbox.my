@props(['name', 'label' => 'Upload file', 'accept' => '.pdf,.jpg,.jpeg,.png', 'maxSize' => '10MB', 'model' => '', 'help' => ''])

<div {{ $attributes->merge(['class' => 'border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition-colors cursor-pointer']) }}
     x-data="{ uploading: false, progress: 0 }"
     x-on:livewire-upload-start="uploading = true"
     x-on:livewire-upload-finish="uploading = false"
     x-on:livewire-upload-error="uploading = false"
     x-on:livewire-upload-progress="progress = $event.detail.progress"
>
    <input type="file" wire:model="{{ $model }}" class="hidden" id="{{ $name }}" accept="{{ $accept }}">

    <template x-if="!uploading">
        <div>
            <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
            <label for="{{ $name }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium cursor-pointer">{{ $label }}</label>
            <p class="text-xs text-gray-400 mt-1">{{ $accept }} up to {{ $maxSize }}</p>
            @if ($help)<p class="text-xs text-gray-400 mt-1">{{ $help }}</p>@endif
        </div>
    </template>
    <template x-if="uploading">
        <div class="space-y-2">
            <p class="text-sm text-gray-500">Uploading...</p>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-blue-600 h-2 rounded-full transition-all" x-bind:style="'width: ' + progress + '%'"></div>
            </div>
        </div>
    </template>
</div>
@error($model)<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
