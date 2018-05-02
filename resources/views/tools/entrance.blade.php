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

            <div class="panel panel-success" style="background-color:#cfc;">
                <div class="panel-body">
                    <h3 class="panel-title"></h3>
                    <p>
                        (5/2) スタンプの表示領域やサイズなどの設定を記憶するようにしました。<br>
                        リセットしたい場合は、設定変更のボタンやスライダーがあるウィンドウを<br>
                        右クリックして「初期設定に戻す」を選択して下さい。
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
