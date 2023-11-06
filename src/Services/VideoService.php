<?php

namespace Itecschool\VideoProcessor\Services;

use Illuminate\Support\Str;
use FFMpeg\Format\Video\X264;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use ProtoneMedia\LaravelFFMpeg\Exporters\EncodingException;
// use Illuminate\Support\Facades\File;

class VideoService
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

        $this->videoIdentifier = Str::random(12);

        $this->s3BasePath = $this->getS3BasePath();

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

    public function getS3BasePath()
    {
        return 'videos'. DIRECTORY_SEPARATOR . $this->videoIdentifier . DIRECTORY_SEPARATOR;
    }

    public function processVideo($videoPath)
    {   

        try {

            // Definir un directorio temporal para el archivo en cuestión
            $newTempDir = storage_path('app/tmp/' . $this->videoIdentifier);

            // Definir las propiedades de configuración
            config(['laravel-ffmpeg.temporary_files_root' => $newTempDir . '/root']);
            
            config(['laravel-ffmpeg.temporary_files_encrypted_hls' => $newTempDir . '/enc']);

            $lowBitrate = (new X264)->setKiloBitrate(250);
            $midBitrate = (new X264)->setKiloBitrate(500);
            $highBitrate = (new X264)->setKiloBitrate(1000);
            $superBitrate = (new X264)->setKiloBitrate(1500);


            $conv = FFMpeg::fromDisk('local')
                ->open($videoPath)
                ->exportForHLS()
                ->setSegmentLength(10) 
                ->setKeyFrameInterval(48) 
                ->withRotatingEncryptionKey(function ($filename, $contents) /*use ($video)*/ {

                    Storage::put('keys' . DIRECTORY_SEPARATOR . $filename, $contents);

                })
                ->addFormat($lowBitrate, function($media) {
                    $media->scale(640,360);
                })
                ->addFormat($midBitrate, function($media) {
                    $media->scale(842, 480);
                })
                ->addFormat($highBitrate, function ($media) {
                    $media->scale(1280, 720);
                })
                ->addFormat($superBitrate, function($media) {
                    $media->scale(1920, 1080);
                });

                dd($conv);
                
                //->save('hls' . DIRECTORY_SEPARATOR . $this->videoIdentifier . '.m3u8');

            // Limpar los archivos temporales
            FFMpeg::cleanupTemporaryFiles();

        } catch (EncodingException $exception) {

            $command = $exception->getCommand();

            $errorLog = $exception->getErrorOutput();

            dd($exception, $command, $errorLog);
        }

    }

}
