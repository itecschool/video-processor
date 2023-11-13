<?php

namespace Itecschool\VideoProcessor\Jobs;

use App\Models\Video;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;

class ProcessVideoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $video;

    public function __construct($videoId)
    {
        $this->video = Video::findOrFail($videoId);
    }

    public function handle()
    {

        $this->video->update([
            'status' => 'queue_for_processing',
        ]);

        // Llamada al comando
        Artisan::call('video:process', [
            'videoId' => $this->video->id
        ]);
    }
}
