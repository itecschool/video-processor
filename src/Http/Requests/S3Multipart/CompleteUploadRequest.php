<?php

namespace Itecschool\VideoProcessor\Http\Requests\S3Multipart;

class CompleteUploadRequest extends CustomFormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'video_identifier' => 'required|string',
            'upload_id' => 'required|string',
            'parts' => 'required|array',
        ];
    }

    public function handle()
    {
        
        $this->s3->completeMultipartUpload([
            'Bucket' => $this->bucket,
            'Key' => $this->getKey($this->video_identifier),
            'UploadId' => $this->upload_id,
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
