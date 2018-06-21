<?php

namespace App\Http\Controllers;

use App\Room;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $room = auth()->user()->room;

        return view('home', compact('room'));
    }

    public function listener(Room $room, $tag = null)
    {
        $noTags = 'false';

        return view('listener', compact('room', 'tag', 'noTags'));
    }

    public function listenerNoTags(Room $room)
    {
        $tag = null;
        $noTags = 'true';

        return view('listener', compact('room', 'tag', 'noTags'));
    }

    public function broadcaster(Room $room)
    {
        return view('broadcaster', compact('room'));
    }

    public function broadcasterBeta(Room $room)
    {
        return view('broadcaster_beta', compact('room'));
    }
}
