<?php

namespace Itecschool\VideoProcessor\Listeners;

use Itecschool\VideoProcessor\Jobs\ProcessVideoJob;
use Itecschool\VideoProcessor\Events\VideoUploadSuccessful;

class ProcessVideoHLS
{
    /**
     * Handle the event.
     *
     * @param  VideoUploadSuccessful  $event
     * @return void
     */
    public function handle(VideoUploadSuccessful $event)
    {
        // Accede al ID del video desde el evento
        $videoId = $event->videoId;

        // Llamada al comando
        ProcessVideoJob::dispatch($videoId);
    }
}
