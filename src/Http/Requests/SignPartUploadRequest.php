<?php

namespace Itecschool\VideoProcessor\Http\Requests;

class SignPartUploadRequest extends CustomFormRequest
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
            'partNumber' => 'required|integer',
        ];
    }

    public function handle()
    {
        
        $url = $this->s3->createPresignedRequest(
            $this->s3->getCommand('UploadPart', [
                'Bucket' => $this->bucket,
                'Key' => $this->filename,
                'UploadId' => $this->uploadId,
                'PartNumber' => $this->partNumber,
            ]),
            '+20 minutes'
        )->getUri();

        return ['url' => $url];
    }
}
