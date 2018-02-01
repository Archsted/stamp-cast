<?php

namespace App\Http\Controllers;

use App\Http\Requests\DestroyFavorite;
use App\Http\Requests\StoreFavorite;
use App\User;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        $query = $user->favorites();

        // ルームIDの指定がある場合、フィルタリングする
        if ($request->has('room_id')) {
            $query->available($request->room_id);
        }

        $favorites = $query->get();

        return [
            'favorites' => $favorites,
        ];
    }

    public function store(StoreFavorite $request)
    {
        /** @var User $user */
        $user = $request->user();

        $user->favorites()->attach($request->stamp_id);
    }

    public function destroy(DestroyFavorite $request)
    {
        /** @var User $user */
        $user = $request->user();

        $user->favorites()->detach($request->stamp_id);
    }
}
