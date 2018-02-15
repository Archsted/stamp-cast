<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title></title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Styles -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <style>
        body {
            background-color: transparent !important;
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

        .form {
            background-color: #ddf;
            padding: 20px;
            border: solid 4px #00a;
            -webkit-border-radius: 8px;
            -moz-border-radius: 8px;
            border-radius: 8px;
        }


    </style>
</head>
<body>
<div id="app">
    <div class="flex-center position-ref full-height">
        <div class="title m-b-md form">

            <div class="panel panel-danger" style="background-color:#fcc;">
                <div class="panel-body">
                    <h3 class="panel-title">【重要】サーバの高負荷対策について</h3>
                    <p>
                        現在夜間を中心にサーバへの負荷が高く、今後のサービス維持が困難な状況となっております。<br>
                        つきましてはその原因の特定と、対策による効果を調べるため、<br>
                        日替わりで機能の制限を行わせていただくことになりました。<br>
                        使いづらいと感じる事もあるかと思いますが、その結果で通常時と<br>
                        どの程度の違いが出るのかを調べるため、ご理解とご協力をお願いします。
                    </p>

                    <p>
                        <strong>2月15日の15時頃〜2月16日の日中にかけて、<br>
                            スタンプのアップロード機能（新規登録）を一時停止します</strong>。<br>
                        ご不便をおかけしますが、よろしくお願いします。
                    </p>
                </div>
            </div>

            <form action="{{ route('receiver') }}" method="post">
                {{ csrf_field() }}
                <p style="font-size:140%; margin:0;"><label for="url">スタンプルームURL</label></p>
                <input type="text" name="url" value="{{ session('url', '') }}" id="url" size="40" placeholder="" autofocus> <input type="submit" value="受信する"><br>
                例）https://stamp.archsted.com/1
                @if(session('message'))
                    <p style="margin:0; padding: 0; color: red;"><strong>{{ session('message') }}</strong></p>
                @endif
            </form>
        </div>
    </div>
</div>
<script src="{{ mix('js/app.js') }}"></script>
</body>
</html>
