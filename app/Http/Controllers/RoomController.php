<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoomRequest;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function create()
    {
        return view('room.create');
    }

    public function store(StoreRoomRequest $request)
    {


    }
}
