<?php

namespace App\Livewire\Trips;

use App\Models\Trip;
use App\Models\Document;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Storage;
use App\Services\NotificationService;

class TripFiles extends Component
{
    use WithFileUploads;

    public Trip $trip;
    public $upload = null;
    public string $category = 'other';
    public string $title = '';

    public function mount(Trip $trip): void
    {
        $this->trip = $trip->load('documents');
    }

    public function uploadFile(): void
    {
        $this->validate([
            'upload' => 'required|file|max:10240',
            'category' => 'required|string',
            'title' => 'nullable|string|max:255',
        ]);

        $path = $this->upload->store('trip-files/'.$this->trip->id, 'public');

        Document::create([
            'trip_id' => $this->trip->id,
            'type' => $this->category,
            'title' => $this->title ?: $this->upload->getClientOriginalName(),
            'file_path' => $path,
            'mime_type' => $this->upload->getMimeType(),
            'size' => $this->upload->getSize(),
        ]);

        $this->trip->logTimeline('file_added', "Uploaded file: {$this->title}");
        app(NotificationService::class)->fileUploaded($this->trip, $this->title);

        $this->reset(['upload', 'title', 'category']);
        $this->trip->load('documents');
    }

    public function deleteFile(string $id): void
    {
        $doc = Document::findOrFail($id);
        Storage::disk('public')->delete($doc->file_path);
        $doc->delete();
        $this->trip->load('documents');
    }

    public function render()
    {
        return view('livewire.trips.trip-files');
    }
}
