<?php

return [

	// Esto es necesario para el proceso de migración de videos de vimeo a iTec School
	'vimeo_token' => '7f077f6691f17823291611e6597d0d16', // iTec School VA

	'ffmpeg_path' => env('FFMPEG_PATH', 'C:/xampp/htdocs/github/itecschool/video-processor/bin/ffmpeg/ffmpeg.exe'),

	'ffprobe_path' => env('FFPROBE_PATH', 'C:/xampp/htdocs/github/itecschool/video-processor/bin/ffmpeg/ffprobe.exe'),

	's3_url' => env('S3_URL', 'https://itecschool.s3.amazonaws.com'), 

    'cloudfront_url' => env('CLOUDFRONT_URL', 'd1st6n2eacne1j.cloudfront.net'),

    'video_path' => env('VIDEO_PATH', 'videos'),
	
];