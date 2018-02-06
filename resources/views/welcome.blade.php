<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>StampCast</title>

        <script defer src="https://use.fontawesome.com/releases/v5.0.4/js/all.js"></script>

        <!-- Fonts -->
        <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Raleway:100,600" type="text/css">
        <link rel="stylesheet" href="//fonts.googleapis.com/earlyaccess/notosansjp.css" type="text/css">

        <style>
            @import url(http://fonts.googleapis.com/earlyaccess/notosansjp.css);
        </style>

        <!-- Styles -->
        <style>
            html, body {
                background: radial-gradient(#636b6f, #232b2f) fixed;
                color: #fff;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
                font-family: 'Raleway', sans-serif;
                font-weight: 100;
            }

            .links > a {
                color: #fff;
                padding: 0 25px;
                font-size: 12px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }

            img.stamp {
                position: absolute;
                opacity: 0;
                -webkit-user-select: none;
                -moz-user-select: none;
                -ms-user-select: none;
                user-select: none;
            }

            .container {
                padding: 20px;
                font-family: 'Noto Sans JP';
            }

            .container p {
                text-indent: 1em;
            }

            .feature .container {
                width: 1024px;
                margin: 0 auto;
            }

        </style>
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
                        このWebサイトと、送られたスタンプをデスクトップ上に表示するツールの２つから成ります。</p>

                    <p>リスナー側は特別なソフトを必要としません。スタンプの送信や追加は全てブラウザ上から行えるため、リスナーは配信者が提示する本Webサイト上のURLにアクセスするだけでご利用可能です。</p>

                    <p>配信者側は、リスナーから送られたスタンプをデスクトップ上に表示するツールをダウンロードし起動しておきます。
                        初回のみ、本Webサイト上で会員登録を行い、ルームと呼ばれる自分専用の（リスナーにアクセスしてもらう）スタンプ一覧ページを作る必要があります。</p>
                </div>

            </div>

        </div>
        <script src="{{ mix('js/top.js') }}"></script>
    </body>
</html>
