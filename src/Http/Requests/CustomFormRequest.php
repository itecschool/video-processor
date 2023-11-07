<?php

namespace Itecschool\VideoProcessor\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Itecschool\VideoProcessor\Support\Traits\S3Client;

class CustomFormRequest extends FormRequest
{

    use S3Client;

	protected $s3;

    protected $bucket;

    public function __construct()
    {
        $this->s3 = $this->s3Client();

        $this->bucket = $this->s3Bucket();
    }
    
}