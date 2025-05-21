<?php

namespace App\Events;

use App\Models\CsvUpload;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CsvUploadStatusChanged
{
    use Dispatchable, SerializesModels;

    public  $csvUpload;
    public  $newStatus;

    public function __construct(CsvUpload $csvUpload)
    {
        $this->csvUpload = $csvUpload;
        $this->newStatus = $csvUpload->status_id;
    }
}
