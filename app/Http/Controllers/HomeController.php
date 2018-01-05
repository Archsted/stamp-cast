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
        return view('home');
    }

    public function listener($code)
    {
        $room = Room::where('code', $code)
            ->firstOrFail();

        return view('listener', compact('room'));
    }

    public function broadcaster($code)
    {
        $room = Room::where('code', $code)
            ->firstOrFail();

        return view('broadcaster', compact('room'));
    }
}
