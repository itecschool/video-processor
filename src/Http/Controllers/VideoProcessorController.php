<?php

namespace Itecschool\VideoProcessor\Http\Controllers;



class VideoProcessorController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

}
