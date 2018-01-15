@extends('layouts.app')

@section('content')
<div class="container">
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    {{--
    <div class="jumbotron">
        <h1>StampCastへようこそ！</h1>
        <p class="lead">もし貴方が配信者でスタンプを表示したい場合は、まずはルームを作成してください。
            ルームはリスナーに公開する、スタンプ送信フォームページとなります。</p>
        <p><a class="btn btn-lg btn-success" href="{{ route('room_create') }}" role="button">ルームを作成する</a></p>
    </div>
    --}}

</div>
@endsection
