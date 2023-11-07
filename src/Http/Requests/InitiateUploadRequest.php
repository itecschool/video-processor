<?php

namespace Itecschool\VideoProcessor\Http\Requests;

class InitiateUploadRequest extends CustomFormRequest
{

    public function authorize()
    {
        return true;  // Modificar según tus necesidades.
    }

    public function rules()
    {
        return [
            'filename' => 'required|string',
        ];
    }

    public function handle()
    {
        
        $result = $this->s3->createMultipartUpload([
            'Bucket' => $this->bucket,
            'Key' => $this->filename,
        ]);

        return ['uploadId' => $result['UploadId']];
    }

}