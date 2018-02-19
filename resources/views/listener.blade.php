@extends('layouts.app')

@section('title')
    {{ config('app.name', 'StampCast') }} - {{$room->name}}
@endsection

@section('content')
    <div class="container-fluid">

        <div class="panel panel-danger">
            <div class="panel-heading">
                <h3 class="panel-title">【重要】サーバの高負荷対策について</h3>
            </div>
            <div class="panel-body">
                <p>
                    現在夜間を中心にサーバへの負荷が高く、今後のサービス維持が困難な状況となっております。
                    少しずつ対策を施し改善されてはおりますが、負荷が大きい一部機能を制限・調整させていただいております。
                </p>
                <p>
                    ■現在の制限や調整項目<br>
                    ・ページ表示モードを使えなくしてあります。<br>
                    ・スタンプ一覧の表示限界数を300前後に設定しています。<br>
                    ・連投制限の回数や間隔を調整中
                </p>
            </div>
        </div>

        <div class="panel panel-success">
            <div class="panel-heading">
                <h3 class="panel-title">更新情報</h3>
            </div>

            <div class="panel-body">
                <p>
                    (2/18 14:37) 一覧ページの重さ低減のため、アニメgifのサムネイル表示を静止画にして、マウスオーバー中にのみ再生表示するようにしました。アニメgifの場合はサムネイル中央にマークが出ます。
                </p>
                <p>
                    スタンプの左下のボタンからオリジナルサイズを表示できるようになりました。スタンプを保存したい時はこちらからどうぞ。<br>
                    <strong>一覧の表示からそのまま保存してしまうと、サムネイルの荒いファイルが保存されてしまいます。</strong>
                </p>
            </div>
        </div>


        <div class="room-info">
            <h3>{{$room->name}}
                @auth
                    @if( auth()->user()->id === $room->user_id )
                        <small>
                            <a class="btn btn-success btn-sm" href="{{ route('room_edit', ['room' => $room->id]) }}" role="button">
                                <i class="fas fa-edit"></i> ルーム設定を変更する
                            </a>
                        </small>
                    @endif
                @endauth
            </h3>

            @if(strlen($room->description) > 0)
            <div class="room-description">
                {!! nl2br(htmlspecialchars($room->description)) !!}
            </div>
            @endif
        </div>

        <stamp-list
                ref="stampList"
                :room="{id: {{$room->id}}, userId: {{ $room->user_id }}}"
                :room-id="{{ $room->id }}"
                :uploader-level="{{ $room->uploader_level }}"
                :imprinter-level="{{ $room->imprinter_level }}"
                @auth
                :user-id="{{ auth()->user()->id }}"
                @endauth
                @guest
                :user-id="null"
                @endguest
        ></stamp-list>
    </div>
@endsection

@section('footer_script')
    <script src="//www.promisejs.org/polyfills/promise-7.0.4.min.js"></script>
    <script src="{{ mix('js/listener.js') }}"></script>
@endsection