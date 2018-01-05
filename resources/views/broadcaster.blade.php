<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="//{{ Request::getHost() }}:6001/socket.io/socket.io.js"></script>
    <title>スタンプ表示用透過ウィンドウ</title>
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <style>
        body {
            background-color:rgba(0,0,0,0);
            overflow: hidden;
        }

        button {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        img.stamp {
            position: absolute;
            opacity: 0;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        .stampArea {
            width: 100%;
            height: 100%;
        }

        .stampAreaInfo > div {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
    </style>
<body>
<div id="app">
    <stamp-display ref="stamp" room-id="{{ $room->id }}"></stamp-display>
</div>
<script src="{{ asset('js/broadcaster.js') }}"></script>
</body>
</html>