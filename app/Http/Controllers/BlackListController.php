<?php

namespace App\Http\Controllers;

use App\BlackListIp;
use App\Stamp;
use App\User;
use Illuminate\Http\Request;

class BlackListController extends Controller
{
    public function store(Request $request)
    {
        $stamp = Stamp::withoutGlobalScope('softDelete')->findOrFail($request->get('stamp_id'));

        /** @var User $user */
        $user = $request->user();

        if (is_null($stamp->user_id)) {
            // スタンプが未登録ユーザによるアップロードだった場合
            //$blackListIp = new BlackListIp(['ip' => $stamp->ip]);
            $blackListIp = BlackListIp::firstOrNew(['ip' => $stamp->ip]);

            $user->blackListIps()->save($blackListIp);
        } else {
            // スタンプが登録済みユーザによるアップロードだった場合
            $targetIds = $user->blackListUsers->pluck('id');
            $targetIds[] = $stamp->user_id;

            $user->blackListUsers()->sync($targetIds);
        }
    }

    public function destroy(Request $request)
    {

    }
}