<?php

namespace App\Http\Controllers;

use App\Stamp;
use App\TwitterToken;
use Illuminate\Http\Request;
use Socialite;
use Twitter;

class TwitterTokenController extends Controller
{
    public function redirectToTwitter()
    {
        $twitterToken = request()->user()->twitterToken;

        if (is_null($twitterToken)) {
            return Socialite::driver('twitter')->redirect();
        } else {
            abort(403, '既にTwitterと連携済みです。');
        }
    }

    public function handleTwitterCallback()
    {
        $user = Socialite::driver('twitter')->user();

        TwitterToken::create([
            'user_id' => request()->user()->id,
            'token' => $user->token,
            'secret' => $user->tokenSecret,
        ]);

        return redirect()->route('apps')->with('message', 'Twitterとの連動が完了しました。');
    }

    public function send(Request $request)
    {
        $twitterToken = $request->user()->twitterToken;

        if (is_null($twitterToken)) {
            abort(403, '再度アプリ連携を行ってください。');
        }

        Twitter::reconfig(['token' => $twitterToken->token, 'secret' => $twitterToken->secret]);

        $stamp = Stamp::findOrFail($request->stamp_id);

        try {
            $uploadedMedia = Twitter::uploadMedia(['media' => file_get_contents($stamp->name)]);

            Twitter::postTweet([
                'status' => $request->message,
                'media_ids' => $uploadedMedia->media_id_string,
            ]);
        } catch (\Exception $exception) {
            $twitterToken->delete();
            abort(400, '投稿に失敗しました。再度アプリ連携を行ってください。');
        }
    }

    public function clearToken()
    {
        $twitterToken = request()->user()->twitterToken;

        if (is_null($twitterToken)) {
            $message = 'Twitterとの連動解除に失敗しました。';
        } else {
            $twitterToken->delete();
            $message = 'Twitterとの連動を解除しました。';
        }

        return redirect()->route('apps')->with('message', $message);
    }
}
