<?php

namespace Itecschool\VideoProcessor\Support\Traits;

use Aws\S3\S3Client as AwsS3Client;

trait S3Client
{

	public function s3Client()
	{
		return new AwsS3Client([
            'version' => 'latest',
            'region' => config('filesystems.disks.s3.region'),
            'credentials' => [
                'key' => config('filesystems.disks.s3.key'),
                'secret' => config('filesystems.disks.s3.secret'),
            ],
        ]);
	}

	public function s3Bucket()
	{
        return config('filesystems.disks.s3.bucket');
	}

}