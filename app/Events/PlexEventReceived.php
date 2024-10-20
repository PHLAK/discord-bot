<?php

namespace App\Events;

use App\File;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\UploadedFile;
use Illuminate\Queue\SerializesModels;
use stdClass;

class PlexEventReceived
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public stdClass $payload;
    public ?File $file = null;

    /** Create a new event instance. */
    public function __construct(stdClass $payload, UploadedFile $file = null)
    {
        $this->payload = $payload;

        if ($file instanceof UploadedFile) {
            $this->file = File::createFromUploadedFile($file);
        }
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('plex-events');
    }
}
