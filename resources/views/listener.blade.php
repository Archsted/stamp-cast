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

                <p>
                    (2/16 17:47) 現在、試験的に以下の制限を行っています。転送量が増えており、対策を行うまでの暫定処置です。<br>
                    <strong>・ページ表示を削除</strong><br>
                    <strong>・無限スクロールの表示上限を300前後に</strong>
                </p>

                <p>
                    また、会員登録を行うと、スタンプのお気に入り登録機能が使えるようになります。（ログイン状態でスタンプにカーソルを合わせ、右上のハートをクリック）<br>
                    お気に入りのスタンプのみを表示することもできるので、スタンプが増えて見つけにくいという方はご活用ください。<br>
                    （今後、タグによる検索機能の追加を考えています）
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