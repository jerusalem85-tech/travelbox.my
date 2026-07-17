<div>
    <div class="space-y-3">
        @if ($showForm)
        <div class="p-4 bg-gray-50 rounded-lg border border-gray-200 mb-3">
            <form wire:submit="save" class="space-y-3">
                <div>
                    <label class="block text-xs font-medium text-gray-700">Title *</label>
                    <input type="text" wire:model="title" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" />
                    @error('title') <p class="text-xs text-red-600 mt-0.5">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700">Description</label>
                    <textarea wire:model="description" rows="2" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700">Priority</label>
                        <select wire:model="priority" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm">
                            <option value="low">Low</option><option value="medium">Medium</option><option value="high">High</option><option value="urgent">Urgent</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700">Due Date</label>
                        <input type="date" wire:model="due_date" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" />
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" class="px-3 py-1.5 text-sm font-medium text-white bg-blue-600 rounded hover:bg-blue-700">{{ $editingId ? 'Update' : 'Add' }}</button>
                    <button type="button" wire:click="resetForm" class="px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">Cancel</button>
                </div>
            </form>
        </div>
        @endif

        @if ($trip->tasks->isEmpty())
        <p class="text-sm text-gray-500 text-center py-4">No tasks yet.</p>
        @else
        <div class="space-y-2">
            @foreach ($trip->tasks as $task)
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg {{ $task->status === 'completed' ? 'opacity-60' : '' }}">
                <div class="flex items-center gap-3 min-w-0">
                    <button wire:click="toggleComplete('{{ $task->id }}')" class="shrink-0 w-4 h-4 rounded border border-gray-400 flex items-center justify-center {{ $task->status === 'completed' ? 'bg-green-500 border-green-500' : '' }}">
                        @if ($task->status === 'completed')
                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                        @endif
                    </button>
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-gray-900 {{ $task->status === 'completed' ? 'line-through' : '' }}">{{ $task->title }}</p>
                        @if ($task->description)<p class="text-xs text-gray-500 truncate">{{ $task->description }}</p>@endif
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <span class="text-xs font-medium px-1.5 py-0.5 rounded {{ match($task->priority) { 'urgent' => 'bg-red-100 text-red-700', 'high' => 'bg-orange-100 text-orange-700', 'medium' => 'bg-blue-100 text-blue-700', default => 'bg-gray-100 text-gray-600' } }}">{{ ucfirst($task->priority) }}</span>
                    @if ($task->due_date)<span class="text-xs text-gray-400">{{ $task->due_date->format('M d') }}</span>@endif
                    <button wire:click="edit('{{ $task->id }}')" class="p-1 text-gray-400 hover:text-amber-600"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg></button>
                    <button wire:click="delete('{{ $task->id }}')" wire:confirm="Delete this task?" class="p-1 text-gray-400 hover:text-red-600"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg></button>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        <button wire:click="openForm" class="text-xs text-blue-600 hover:underline">+ Add Task</button>
    </div>
</div>
