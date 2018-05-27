<?php

namespace App\Http\Controllers;

use App\BlackListIp;
use App\Http\Requests\DestroyBlackList;
use App\Imprint;
use App\Stamp;
use App\User;
use Illuminate\Http\Request;

class BlackListController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = request()->user();

        $blackListIps = $user->blackListIps()->latest()->get();

        return view('blackList.index', compact('blackListIps'));
    }

    public function store(Request $request)
    {
        $stamp = Stamp::withoutGlobalScope('softDelete')->findOrFail($request->get('stamp_id'));

        /** @var User $user */
        $user = $request->user();

        // （念のため）スタンプのアップロード者のIPが自分のIPと異なる場合
        if ($stamp->ip !== $request->ip()) {
            $blackListIp = BlackListIp::firstOrNew(['ip' => $stamp->ip]);

            $user->blackListIps()->save($blackListIp);
        } else {
            abort(403, '自分のIPアドレスと同一です。');
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
        } else {
            abort(403, '自分のIPアドレスと同一です。');
        }
    }

    /**
     * @param BlackListIp $blackList
     * @param DestroyBlackList $request
     * @throws \Exception
     */
    public function destroy(BlackListIp $blackList, DestroyBlackList $request)
    {
        $blackList->delete();
    }
}
