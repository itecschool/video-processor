<?php

use Illuminate\Support\Facades\Route;

use Illuminate\Http\UploadedFile;
use Itecschool\VideoProcessor\Services\VideoService;

Route::get('test', function(VideoService $videoService) {

	$videoPath = "videos/test2.mp4";

	$res = $videoService->processVideo($videoPath);

	dd($res);

});