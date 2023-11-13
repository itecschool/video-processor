<?php

namespace Itecschool\VideoProcessor\Http\Controllers;

use App\Models\Video;
use Itecschool\VideoProcessor\Services\VideoService;

class VideoController extends Controller
{

    protected $videoService;
    
    public function __construct(VideoService $videoService)
    {
        $this->middleware('auth:sanctum');

        $this->videoService = $videoService;
    }

    public function player($code) 
    {
        $video = Video::where('code', $code)->firstOrFail();

        return view('videoprocessor::player', ['video' => $video]);
    }

    public function playlist($code, $filename)
    {
        return $this->videoService->playerResponse($code, $filename);
    }

    public function key($code, $key)
    {
        return $this->videoService->keyResponse($code, $key);
    }

}
