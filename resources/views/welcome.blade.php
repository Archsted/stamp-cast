<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'StampCast') }}</title>
        <link href="{{ mix('css/top.css') }}" rel="stylesheet">
        <script defer src="https://use.fontawesome.com/releases/v5.0.4/js/all.js"></script>
    </head>
    <body>
        <div id="app">
            <div class="logo flex-center position-ref full-height">
            @if (Route::has('login'))
                <div class="top-right links">
                @auth
                    <a href="{{ url('/home') }}"><i class="fas fa-home fa-lg"></i> ホーム</a>
                @else
                    <a href="{{ route('login') }}"><i class="fas fa-sign-in-alt fa-lg"></i> ログイン</a>
                    <a href="{{ route('register') }}"><i class="fas fa-user-plus fa-lg"></i> 新規登録</a>
                @endauth
                </div>
            @endif
                <div class="content">
                    <div class="title m-b-md">
                        Stamp Cast
                    </div>
                </div>
            </div>

            <div class="feature flex-center position-ref full-height">
                <div class="container">
                    <h2>StampCast（スタンプキャスト）とは？</h2>

                    <p>リスナーから送信されたスタンプ画像を配信者のデスクトップ上に表示するシステムです。
                        このWebサイトと、送られたスタンプをデスクトップ上に表示するツールの２つから構成されます。</p>

                    <p style="text-align: center; margin: 30px auto;"><img src="/images/stampcast_01.jpg" alt="スタンプ送信フォーム"><br>
                        スタンプ送信画面の一例</p>

                    <p>リスナー側は特別なソフトを必要としません。スタンプの送信や追加は全てブラウザ上から行えるため、
                        配信者が提示するURLにアクセスするだけで利用できます。</p>

                    <p>配信者側は、リスナーから送られたスタンプをデスクトップ上に表示するツールをダウンロードし起動しておきます。
                        初回のみ、本Webサイト上で会員登録を行い、ルームと呼ばれる自分専用の
                        （リスナーにアクセスしてもらう）スタンプ一覧ページを作る必要があります。</p>
                </div>
            </div>

        </div>
        <script src="{{ mix('js/top.js') }}"></script>
    </body>
</html>
