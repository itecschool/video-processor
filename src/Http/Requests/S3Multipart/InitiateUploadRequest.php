<?php

namespace Itecschool\VideoProcessor\Http\Requests\S3Multipart;

class InitiateUploadRequest extends CustomFormRequest
{

    public function authorize()
    {
        return true;  // Modificar según tus necesidades.
    }

    public function rules()
    {
        return [
            'video_identifier' => 'required|string|unique:videos,code', 
        ];
    }

    public function handle()
    {
        
        $result = $this->s3->createMultipartUpload([
            'Bucket' => $this->bucket,
            'Key' => $this->getKey($this->video_identifier),
        ]);

        return ['upload_id' => $result['UploadId']];
    }

}