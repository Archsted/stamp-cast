<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
@if (env('APP_ENV') === 'production')
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-116038527-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'UA-116038527-1');
    </script>
@endif
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
    @section('title')
        {{ config('app.name', 'StampCast') }}
    @show
    </title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="//www.promisejs.org/polyfills/promise-7.0.4.min.js"></script>

    <script src="//{{ Request::getHost() }}:6001/socket.io/socket.io.js"></script>

    <!-- Styles -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <script defer src="https://use.fontawesome.com/releases/v5.0.4/js/all.js"></script>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-default navbar-static-top">
            <div class="container">
                <div class="navbar-header">
                    <!-- Collapsed Hamburger -->
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse" aria-expanded="false">
                        <span class="sr-only">Toggle Navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>

                    <!-- Branding Image -->
                    <a class="navbar-brand" href="{{ url('/') }}">
                        <span><img src="/images/logo.png" alt="logo" style="padding-bottom: 6px; margin-right: 4px;">{{ config('app.name', 'Laravel') }}</span>
                    </a>
                </div>

                <div class="collapse navbar-collapse" id="app-navbar-collapse">
                    <!-- Left Side Of Navbar -->
                    <ul class="nav navbar-nav">
                        @auth
                            <li><a href="{{ route('login') }}"><i class="fas fa-home fa-lg"></i> ホーム</a></li>
                            @if(Auth::user()->room)
                                <li>
                                    <a href="{{ route('listener', ['room' => Auth::user()->room->id]) }}">
                                        <i class="fas fa-laptop fa-lg"></i> スタンプルーム
                                    </a>
                                </li>
                            @else
                                <li>
                                    <a href="{{ route('room_create') }}">
                                        <i class="fas fa-laptop fa-lg"></i> スタンプルーム作成
                                    </a>
                                </li>
                            @endif
                        @endauth
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="nav navbar-nav navbar-right">
                        <!-- Authentication Links -->
                        <li><a href="/documents"><i class="fas fa-book fa-lg"></i> ドキュメント</a></li>
                        @guest
                            <li><a href="{{ route('login') }}"><i class="fas fa-sign-in-alt fa-lg"></i> ログイン</a></li>
                            <li><a href="{{ route('register') }}"><i class="fas fa-user-plus fa-lg"></i> 新規登録</a></li>
                        @else
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true">
                                    <i class="fas fa-user-circle fa-lg"></i> {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="{{ route('logout') }}"
                                           onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                            <i class="fas fa-sign-out-alt fa-lg"></i> ログアウト
                                        </a>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                            {{ csrf_field() }}
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @endguest
                    </ul>
                </div>

            </div>
        </nav>

        @yield('content')
    </div>

    <footer class="footer">
        <div class="text-center">
            <p class="text-muted">作：会長 / Archsted ( <a href="https://twitter.com/etude_kaicho">@etude_kaicho</a> )</p>
        </div>
    </footer>

    <!-- Scripts -->
    @section('footer_script')
        <script src="{{ mix('js/app.js') }}"></script>
    @show
</body>
</html>
