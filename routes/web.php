<?php

use Illuminate\Support\Facades\Route;

// Testing

use Illuminate\Support\Facades\Artisan;
use Itecschool\VideoProcessor\Services\VideoService;
use Itecschool\VideoProcessor\Jobs\VimeoVideoUploadJob;

Route::get('test', function(VideoService $videoService) {
	/*
	Artisan::call('vimeo:upload', [
            'videoId' => 5
        ]);

	dd();
	*/

	\App\Models\Video::where('status', 'active')
		->where('cloud', 'vimeo')
		->get()
		->map(function( $video ) {

		VimeoVideoUploadJob::dispatch($video->id);

	});

});

// Rutas de la aplicación

Route::post('initiate-upload', 'S3MultipartController@initiateUpload')
	->name('initiate.upload');

Route::post('sign-part-upload', 'S3MultipartController@signPartUpload')
	->name('sign.part.upload');

Route::post('complete-upload', 'S3MultipartController@completeUpload')
	->name('complete.upload');


// Procesamiento y reproducción

Route::get('player/{code}', 'VideoController@player')
	->name('player');

Route::get('playlist/{code}/{filename}', 'VideoController@playlist')
	->name('playlist');

Route::get('secret/{code}/{key}', 'VideoController@key')
	->name('key');
