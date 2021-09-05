<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\UploadedFile;
use Illuminate\Queue\SerializesModels;

class PlexEventReceived
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public object $payload;
    public ?string $fileName;
    public ?string $fileContent;

    /** Create a new event instance. */
    public function __construct(object $payload, UploadedFile $file = null)
    {
        $this->payload = $payload;
        $this->fileName = $file instanceof UploadedFile ? $file->getClientOriginalName() : null;
        $this->fileContent = base64_encode($file instanceof UploadedFile ? $file->getContent() : null);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('plex-events');
    }
}
