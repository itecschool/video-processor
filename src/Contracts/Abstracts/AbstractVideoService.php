<?php

namespace Itecschool\VideoProcessor\Contracts\Abstracts;

use App\Models\Video;
use Illuminate\Support\Facades\Storage;

abstract class AbstractVideoService
{

	protected $ffmpegPath;

    protected $ffprobePath;

    protected $cloudfrontUrl;

    protected $videoIdentifier;

    protected $s3BasePath;

    public function __construct()
    {
        $this->ffmpegPath = config('videoprocessor.ffmpeg_path'); 

        config(['laravel-ffmpeg.ffmpeg.binaries' => $this->ffmpegPath]);

        $this->ffprobePath = config('videoprocessor.ffprobe_path');

        config(['laravel-ffmpeg.ffprobe.binaries' => $this->ffprobePath]);

        config(['laravel-ffmpeg.log_channel' => 'stack']);

        $this->cloudfrontUrl = config('videoprocessor.cloudfront_url');

        $this->checkDependencies();
    }

    private function checkDependencies()
    {
        if (!$this->isFFmpegAvailable() || !$this->isFFprobeAvailable()) {
            throw new \Exception('FFmpeg 0 FFprobe no está instalado o no es accesible.');
        }
    }

    private function isFFmpegAvailable()
    {
        return file_exists($this->ffmpegPath) && is_executable($this->ffmpegPath);
    }

    private function isFFprobeAvailable()
    {
        return file_exists($this->ffprobePath) && is_executable($this->ffprobePath);
    }

    protected function tempOriginUrl($path) 
    {
        return Storage::disk('s3')->temporaryUrl($path, now()->addMinutes(5));
    }

    protected function getVideoByCode($code)
    {
        return Video::where('code', $code)->firstOrFail();
    }

}