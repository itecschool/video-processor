<?php

use Illuminate\Support\Facades\Route;


// Testing

use Illuminate\Http\UploadedFile;
use Itecschool\VideoProcessor\Services\VideoService;

Route::get('test', function(VideoService $videoService) {

	$res = $videoService->hls();

	dd($res);

});

// Subida

Route::get('upload', function() {

	return view('videoprocessor::upload');

});

Route::post('initiate-upload', 'S3MultipartController@initiateUpload')
	->name('initiate.upload');

Route::post('sign-part-upload', 'S3MultipartController@signPartUpload')
	->name('sign.part.upload');

Route::post('complete-upload', 'S3MultipartController@completeUpload')
	->name('complete.upload');


// Procesamiento y reproducción


Route::get('secret/{id}/{key}', 'VideoProcessorController@key')
	->name('videoprocessor.key');

Route::get('playlist/{id}/{filename}', 'VideoProcessorController@playlist')
	->name('videoprocessor.playlist');

Route::get('player/{id}', 'VideoProcessorController@player')
	->name('videoprocessor.player');
