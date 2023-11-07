<?php

namespace Itecschool\VideoProcessor\Http\Requests;

class CompleteUploadRequest extends CustomFormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'filename' => 'required|string',
            'uploadId' => 'required|string',
            'parts' => 'required|array',
        ];
    }

    public function handle()
    {
        
        $this->s3->completeMultipartUpload([
            'Bucket' => $this->bucket,
            'Key' => $this->filename,
            'UploadId' => $this->uploadId,
            'MultipartUpload' => [
                'Parts' => array_map(function ($part) {
                    return [
                        'ETag' => $part['ETag'],
                        'PartNumber' => $part['PartNumber'],
                    ];
                }, $this->parts),
            ],
        ]);

        return ['message' => 'Upload completed'];
    }
}
