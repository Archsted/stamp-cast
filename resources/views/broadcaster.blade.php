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
    <style>
        body {
            background-color:rgba(0,0,0,0);
        }
        img.stamp {
            position: absolute;
            opacity: 0;
        }
    </style>
<body style="/*-webkit-app-region: drag*/">
<div id="app">
    <div style="padding: 1em; background-color: #aaaaff;">
        <button @click="sendStamp" style="/*-webkit-app-region: no-drag*/">counter: @{{counter}}</button>
    </div>
</div>
<script src="{{ asset('js/broadcaster.js') }}"></script>
</body>
</html>