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
                    つきましてはその原因の特定と、対策による効果を調べるため、日替わりで機能の制限を行わせていただくことになりました。
                    使いづらいと感じる事もあるかと思いますが、その結果で通常時とどの程度の違いが出るのかを調べるため、ご理解とご協力をお願いします。
                </p>
            </div>
        </div>

        <div class="panel panel-success">
            <div class="panel-heading">
                <h3 class="panel-title">更新情報</h3>
            </div>

            <div class="panel-body">
                <p>
                    (2/17 08:10) 転送量軽減のため、一覧表示をサムネイル化しました。一覧では少々荒くなりますが、配信者画面での表示には影響ありません。これまでアップロードされたスタンプも順次サムネイル化していきます。
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