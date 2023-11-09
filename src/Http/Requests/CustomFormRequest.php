<?php

namespace Itecschool\VideoProcessor\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Itecschool\VideoProcessor\Support\Traits\S3Client;

class CustomFormRequest extends FormRequest
{

    use S3Client;

	protected $s3;

    protected $bucket;

    protected $key; // Clave de la ubicación de subida del video

    public function __construct()
    {
        $this->s3 = $this->s3Client();

        $this->bucket = $this->s3Bucket();
    }

    protected function getKey($videoIdentifier)
    {
        // PENDIENTE:
        // Por ahora esto está condicionado a que los vidos en formato MP4
        // Más adelante debemos ver de que manera podemos manejar más formatos
        $videoPath = config('videoprocessor.video_path', 'videos');

        return "$videoPath/$videoIdentifier/original.mp4"; 
    }
    
}