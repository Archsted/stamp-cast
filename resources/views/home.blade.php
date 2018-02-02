@extends('layouts.app')

@section('content')
<div class="container">
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    @if($room)

        <p>{{ $room->name }}</p>
        <p>{{ $room->description }}</p>
        <p><a href="{{ route('listener', ['room' => $room->id]) }}" class="btn btn-default">ルームに移動</a></p>

    @else
        <div class="jumbotron">
            <h1>StampCastへようこそ！</h1>
            <p class="lead">もし貴方が配信者でスタンプを表示したい場合は、まずはルームを作成してください。
                ルームはリスナーに公開する、スタンプ送信フォームページとなります。</p>
            <p><a class="btn btn-lg btn-success" href="{{ route('room_create') }}" role="button">ルームを作成する</a></p>
        </div>
    @endif

        <ul>
            <li><a href="{{ route('my_stamps') }}">自分がアップロードしたスタンプ一覧</a></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
        </ul>



</div>
@endsection
