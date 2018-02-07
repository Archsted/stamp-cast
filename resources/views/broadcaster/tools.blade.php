@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>スタンプ表示ツール</h1>

        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">ツールのダウンロード</h3>
            </div>
            <div class="panel-body">
                <ul>
                    <li>
                        <a href="{{ route('tool_download', ['platform' => 'win-x64']) }}">Windows版（64ビット）</a> v1.0.0 - 2018/02/10
                    </li>
                    <li>
                        <a href="{{ route('tool_download', ['platform' => 'win-ia32']) }}">Windows版（32ビット）</a> v1.0.0 - 2018/02/10
                    </li>
                </ul>
            </div>
        </div>

        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">使い方</h3>
            </div>
            <div class="panel-body">
                <p>ダウンロード後適当なフォルダに解凍して下さい。</p>
                <p>同梱してある<mark>readme.txt</mark>が簡易マニュアルになっています。注意点が載っているのでご一読下さい。</p>
            </div>
        </div>

    </div>
@endsection
