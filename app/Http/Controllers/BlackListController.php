<?php

namespace App\Http\Controllers;

use App\BlackListIp;
use App\Imprint;
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

            // （念のため）スタンプのアップロード者のIPが自分のIPと異なる場合
            if ($stamp->ip !== $request->ip()) {
                $blackListIp = BlackListIp::firstOrNew(['ip' => $stamp->ip]);

                $user->blackListIps()->save($blackListIp);
            } else {
                abort(403, '自分のIPアドレスと同一です。');
            }
        } else {
            // スタンプが登録済みユーザによるアップロードだった場合
            $targetIds = $user->blackListUsers->pluck('id');
            $targetIds[] = $stamp->user_id;

            $user->blackListUsers()->sync($targetIds);

            // ユーザー登録し直しでアップされてしまうので、IPアドレスもブラックリストに追加する
            $blackListIp = BlackListIp::firstOrNew(['ip' => $stamp->ip]);

            $user->blackListIps()->save($blackListIp);
        }
    }

    /**
     * スタンプ送信履歴からブラックリストに追加する
     *
     * @param Request $request
     * @param Imprint $imprint
     */
    public function storeByImprint(Request $request, Imprint $imprint)
    {
        /** @var User $user */
        $user = $request->user();

        if ($imprint->room->user_id !== $user->id) {
            abort(403, '自分のルームへの送信履歴ではありません。');
        }

        // （念のため）スタンプの送信者のIPが自分のIPと異なる場合
        if ($imprint->ip !== $request->ip()) {
            $blackListIp = BlackListIp::firstOrNew(['ip' => $imprint->ip]);
            $user->blackListIps()->save($blackListIp);

            if (!is_null($imprint->user_id) && $user->id !== $imprint->user_id) {
                // スタンプが登録済みユーザによるアップロードだった場合
                $targetIds = $user->blackListUsers->pluck('id');
                $targetIds[] = $imprint->user_id;

                $user->blackListUsers()->sync($targetIds);
            }
        } else {
            abort(403, '自分のIPアドレスと同一です。');
        }
    }

    public function destroy(Request $request)
    {

    }
}
