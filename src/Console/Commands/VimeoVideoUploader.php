<?php

namespace Itecschool\VideoProcessor\Console\Commands;

use App\Models\Video;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Itecschool\VideoProcessor\Events\VideoUploadSuccessful;
use Itecschool\VideoProcessor\Events\VideoUploadFailed;

class VimeoVideoUploader extends Command
{
    protected $video;

    protected $signature = 'vimeo:upload {videoId}';

    protected $description = 'Sube un video de Vimeo a AWS S3';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $videoId = $this->argument('videoId');
        
        $this->video = Video::findOrFail($videoId);

        $videoData = $this->fetchVideoData($this->video->code);

        if ($videoData) {

            $this->uploadVideo($videoData);

        } else {

            $this->handleError('error_get_vimeo_video_data');

        }
    }

    private function fetchVideoData($videoCode)
    {
        // Añadir un mecanismo para la rotación de tokens
        $accessToken = config('videoprocessor.vimeo_token');

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
        ])->get("https://api.vimeo.com/videos/{$videoCode}");

        return $response->successful() ? $response->json() : null;
    }

    private function uploadVideo($videoData)
    {
        foreach ($videoData['download'] as $download) {
        
            if ($download['quality'] == 'source') {

                $this->downloadAndUpload($download['link']);

                return;

            }

        }

        $this->handleError('error_vimeo_video_not_source_found');
    }

    private function downloadAndUpload($link)
    {
        $videoContent = Http::timeout(3600)->get($link);

        if ($videoContent->successful()) {

            $this->updateVideoStatus('upload_started'); // Start upload

            $this->saveVideoToS3($videoContent);

            $this->updateVideoStatus('cloud_uploaded'); // Complete upload

            event(new VideoUploadSuccessful($this->video->id));

        } else {
        
            $this->handleError('error_vimeo_video_download_error');
        
        }
    }

    private function saveVideoToS3($videoContent)
    {
        try {

            $videoCode = $this->video->code;

            $fileName = config('videoprocessor.video_path', 'videos') . "/$videoCode/original.mp4";

            $tempFilePath = tempnam(sys_get_temp_dir(), 'vimeo_') . '.mp4';

            file_put_contents($tempFilePath, $videoContent->body());

            Storage::disk('s3')->putFileAs('', new File($tempFilePath), $fileName, 'private');

            unlink($tempFilePath);

        } catch( \Exception $e){

            $this->handleError('error_s3_upload');

        }
    }

    private function handleError($status)
    {
        event(new VideoUploadFailed($this->video->id));

        $this->updateVideoStatus($status);
    }

    protected function updateVideoStatus($status)
    {
        $this->video->update(['status' => $status]);
    }
}
