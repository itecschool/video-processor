<?php

namespace Itecschool\VideoProcessor\Http\Controllers;

use Itecschool\VideoProcessor\Http\Requests\{
    InitiateUploadRequest,
    SignPartUploadRequest,
    CompleteUploadRequest
};

class S3MultipartController extends Controller
{

    public function initiateUpload(InitiateUploadRequest $request)
    {
        return $request->handle();
    }

    public function signPartUpload(SignPartUploadRequest $request)
    {
        return $request->handle();
    }

    public function completeUpload(CompleteUploadRequest $request)
    {
        return $request->handle();
    }

}