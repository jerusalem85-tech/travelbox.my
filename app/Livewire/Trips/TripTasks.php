<?php

namespace App\Livewire\Trips;

use App\Models\Trip;
use App\Models\Task;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Services\NotificationService;

class TripTasks extends Component
{
    public Trip $trip;
    public bool $showForm = false;
    public ?string $editingId = null;
    public string $title = '';
    public string $description = '';
    public string $priority = 'medium';
    public string $due_date = '';

    public function mount(Trip $trip): void
    {
        $this->trip = $trip->load('tasks');
    }

    public function openForm(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(string $id): void
    {
        $t = Task::findOrFail($id);
        $this->editingId = $id;
        $this->title = $t->title;
        $this->description = $t->description ?? '';
        $this->priority = $t->priority;
        $this->due_date = $t->due_date?->format('Y-m-d') ?? '';
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'due_date' => 'nullable|date',
        ]);

        $data = [
            'title' => $this->title,
            'description' => $this->description ?: null,
            'priority' => $this->priority,
            'due_date' => $this->due_date ?: null,
        ];

        if ($this->editingId) {
            Task::findOrFail($this->editingId)->update($data);
            $this->trip->logTimeline('task_edited', "Updated task: {$this->title}");
        } else {
            $data['trip_id'] = $this->trip->id;
            $data['created_by'] = Auth::id();
            Task::create($data);
            $this->trip->logTimeline('task_added', "Added task: {$this->title}");
            if ($this->due_date) {
                app(NotificationService::class)->taskDueSoon($this->trip, $this->title, \Carbon\Carbon::parse($this->due_date));
            }
        }

        $this->resetForm();
        $this->trip->load('tasks');
    }

    public function toggleComplete(string $id): void
    {
        $t = Task::findOrFail($id);
        if ($t->status === 'completed') {
            $t->update(['status' => 'pending', 'completed_at' => null]);
        } else {
            $t->update(['status' => 'completed', 'completed_at' => now()]);
        }
        $this->trip->load('tasks');
    }

    public function delete(string $id): void
    {
        Task::findOrFail($id)->delete();
        $this->trip->load('tasks');
    }

    public function resetForm(): void
    {
        $this->editingId = null;
        $this->title = '';
        $this->description = '';
        $this->priority = 'medium';
        $this->due_date = '';
        $this->showForm = false;
    }

    public function render()
    {
        return view('livewire.trips.trip-tasks');
    }
}
