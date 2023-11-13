<?php

namespace Itecschool\VideoProcessor\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VideoUploadFailed
{
    use Dispatchable, SerializesModels;

    public $videoId;

    public function __construct($videoId)
    {
        $this->videoId = $videoId;
    }
}
