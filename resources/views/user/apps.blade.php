@extends('layouts.app')

@section('content')
<div class="container">
    <h1>外部アプリ連携</h1>

    @if (session('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    <div class="alert alert-info">
        <p>現在はTwitterとの連携のみです。</p>

        <p>Twitterと連携すると、スタンプ帳からTwitterにスタンプ画像（と、任意でメッセージ）を投稿できます。</p>
    </div>





    @if(request()->user()->twitterToken)
        <a class="btn btn-danger" href="/apps/twitter/logout">Twitterとの連携を解除する</a>
    @else
        <a class="btn btn-primary" href="/apps/twitter">Twitterと連携する</a>
    @endif

</div>
@endsection
