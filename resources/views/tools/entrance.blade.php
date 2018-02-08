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
            <form action="{{ route('receiver') }}" method="post">
                {{ csrf_field() }}
                <p style="font-size:140%; margin:0;"><label for="url">スタンプルームURL</label></p>
                <input type="text" name="url" value="" id="url" size="40" placeholder="" autofocus> <input type="submit" value="受信する"><br>
                例）https://stamp.archsted.com/1
                @if(session('message'))
                    <p>{{ session('message') }}</p>
                @endif
            </form>
        </div>
    </div>
</div>
<script src="{{ mix('js/app.js') }}"></script>
</body>
</html>
