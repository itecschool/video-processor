<?php

namespace Itecschool\VideoProcessor\Services;

use Itecschool\VideoProcessor\Contracts\Abstracts\AbstractVideoService;
use App\Models\Video;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use FFMpeg\Format\Video\X264;

class VideoService extends AbstractVideoService
{

    public function processVideo($videoId)
    {   
        $video = Video::findOrFail($videoId);

        // Eliminar directorios en S3 (no importa si no existen)
        Storage::disk('s3')->deleteDirectory($video->s3_hls_path);
        Storage::disk('s3')->deleteDirectory($video->s3_keys_path);

        $video->update([
            'status' => 'processing_started',
        ]);

        // Configura el formato de video a X264.
        $lowBitrate = new X264('aac', 'libx264');
        $lowBitrate->setKiloBitrate(500);

        $midBitrate = new X264('aac', 'libx264');
        $midBitrate->setKiloBitrate(1000);

        $highBitrate = new X264('aac', 'libx264');
        $highBitrate->setKiloBitrate(1500);

        // Exporta el video a HLS.
        $conv = FFMpeg::fromDisk('s3')
            ->open($video->s3_original_path)
            ->exportForHLS()
            ->onProgress(function ($percentage) use ($video) {
                if($percentage == 100) {
                    DB::table('videos')->where('id', $video->id)->update([
                        'status' => 'processing_completed'
                    ]);
                }
            })
            ->setSegmentLength(10) 
            ->setKeyFrameInterval(48) 
            ->withRotatingEncryptionKey(function ($filename, $contents) use ($video) {

                Storage::disk('s3')->put($video->s3_keys_path . DIRECTORY_SEPARATOR . $filename, $contents);

            })
            //->addFormat($lowBitrate)
            //->addFormat($midBitrate)
            ->addFormat($highBitrate)
            ->save($video->s3_hls_master);

        FFMpeg::cleanupTemporaryFiles();

        DB::table('videos')->where('id', $video->id)->update([
            'cloud' => 'aws',
            'status' => 'available_for_viewing'
        ]);

        return 0;
    }

    public function playerResponse($code, $filename) 
    {
        $video = $this->getVideoByCode($code);

        $path = $video->s3_hls_path . '/' . $filename;

        return FFMpeg::dynamicHLSPlaylist()
            ->fromDisk('s3')
            ->open($path)
            ->setKeyUrlResolver(function ($key) use ($video) {

                return route('videoprocessor.key', [
                    'code' => $video->code,
                    'key' => $key
                ]);

            })
            ->setMediaUrlResolver(function ($mediaFilename) use ($video) {

                $path = $video->s3_hls_path . '/' . $mediaFilename;
                
                return Storage::disk('s3')->temporaryUrl($path, now()->addMinutes(5));

            })
            ->setPlaylistUrlResolver(function ($playlistFilename) use ($video) {

                return route('videoprocessor.playlist', [
                    'code' => $video->code,
                    'filename' => $playlistFilename
                ]);

            });
    }

    public function keyResponse($code, $key) 
    {
        $video = $this->getVideoByCode($code);

        $path = $video->s3_keys_path . '/' . $key;
    
        return Storage::disk('s3')->download($path);
    }

}
