<?php

namespace Itecschool\VideoProcessor\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;
use Validator;

class VideoFileHandler
{
    /**
     * Maneja el archivo o URL proporcionado y devuelve la ruta para procesarlo.
     *
     * @param mixed $input
     * @return string
     * @throws ValidationException
     */
    public function handle($input): string
    {

        if ($input instanceof UploadedFile) {

            //$this->validateUploadedFile($input);
            
            $tempPath = $input->store('temp_videos', 'local');
            
            $processedVideoPath = storage_path('app/' . $tempPath);

        } elseif (filter_var($input, FILTER_VALIDATE_URL)) {

            $tempPath = $this->downloadFromUrl($input);

            //$this->validateLocalFile($tempPath);

            $processedVideoPath = $tempPath;

        } else {

            //$this->validateLocalFile($input);

            $processedVideoPath = $input;

        }

        if (!file_exists($processedVideoPath)) {
            throw new \Exception('El archivo de video procesado no existe.');
        }

        return $processedVideoPath;

    }

    /**
     * Valida un archivo subido usando validaciones de Laravel.
     *
     * @param UploadedFile $file
     * @throws ValidationException
     */
    private function validateUploadedFile(UploadedFile $file)
    {
        $validator = Validator::make(['video' => $file], [
            'video' => 'required|mimes:mp4|max:102400', // 100MB como máximo
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    /**
     * Valida un archivo local.
     * 
     * @param string $filePath
     * @throws ValidationException
     */
    private function validateLocalFile(string $filePath)
    {
        $validator = Validator::make(['video' => new UploadedFile($filePath, basename($filePath))], [
            'video' => 'required|mimes:mp4|max:102400',
        ]);

        if ($validator->fails()) {

            throw new ValidationException($validator);

        }
    }

    /**
     * Descarga un archivo de una URL y devuelve la ruta del archivo descargado.
     *
     * @param string $url
     * @return string
     */
    private function downloadFromUrl(string $url): string
    {
        $tempPath = tempnam(sys_get_temp_dir(), 'video_');

        file_put_contents($tempPath, file_get_contents($url));

        return $tempPath;
    }
}
