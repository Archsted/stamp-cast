@extends('layouts.app')

@section('title')
    {{ config('app.name', 'StampCast') }} - {{$room->name}}
@endsection

@section('og_title')
    <meta property="og:title" content="{{ config('app.name', 'StampCast') }} - {{$room->name}}">
@endsection

@section('content')
    <div class="container-fluid">

        <div class="panel panel-success">
            <div class="panel-heading">
                <h3 class="panel-title">更新情報</h3>
            </div>

            <div class="panel-body" style="height:126px; overflow-y: scroll;">
                <p>
                    (6/26) リスナー側の機能として、スタンプ送信先の固定機能を追加しました。ルーム名の右側にある「ここをスタンプ送信先に固定」ボタンで設定できます。<br>
                    固定したルームとは別のルームから（その別ルーム上にある）スタンプを送信すると、代わりに固定したルームに送信されるようになります。<br>
                    固定設定は一人一人、本人のみ有効で、設定はブラウザを閉じるまで（もしくは一定時間経過するまで）有効です。画面下部に表示される通知欄の解除ボタンで解除できます。<br>
                    またルームを別ウィンドウやタブで複数開いていた場合、固定や解除の反映はそれぞれ再読込が必要です。（画面下部の通知が表示されているか否かで、挙動が確定します）
                </p>

                <p>
                    (6/24) スタンプ帳からTwitterにスタンプ画像を投稿できるようになりました。（会員機能）<br>
                    ログイン後、ヘッダ右上の名前をクリックして「外部アプリ連携」からTwitterと連携後、スタンプ帳から利用可能になります。
                </p>

                <p>
                    (5/27) 配信者向け機能として、ブラックリスト管理機能を追加しました。（ログイン後のホーム画面より遷移可能）<br>
                    間違えて追加してしまった時、あるいはリスナーからの要望で解除したい時、配信者自身が解除することができます。
                </p>

                <p>
                    (5/13) ログインすると使える会員用機能として「スタンプ帳」機能を追加しました。<br>
                    いわゆる「マイリスト」機能で、異なるルームのスタンプも一緒に表示でき、アップロードせずとも別のルームに直接スタンプ送信できるようになります。<br>
                    この機能追加により、お気に入り機能は撤廃されました。また、お気に入りに登録してあったスタンプは、スタンプ帳にデータ移行してありますのでご確認ください。
                </p>

                <p>
                    (4/30) 「送信された順」と「回数順」に表示を切り替えた時の処理ロジックを改善し、速度を向上させました。<br>
                    また、スタンプ通知音の音量を少しだけ調整しました。
                </p>

                <p>
                    (3/21) 各スタンプの左下アイコンにマウスカーソルを合わせると、少し大きなプレビューを表示するようにしました。<br>
                    （画面端のスタンプや、横幅が大きすぎるスタンプのプレビューが画面からはみ出るのは仕様です。）<br>
                    左下のアイコンをクリックで、オリジナルサイズを別ウィンドウで開くのは変わりありません。
                </p>

                <p>
                    (3/20) ルーム名の右に、スタンプ追加とスタンプ送信が可能なユーザ種別を表示するようにしました。（各ルームの持ち主が設定してある値です）
                </p>

                <p>
                    (3/18) タグ一覧が表示されているサイドバーを開閉できるようになりました。開閉状態はルーム別に保存されます。
                </p>

                <p>
                    (3/17) タグ一覧に「タグが設定されていないもの」を追加しました。まだスタンプが一つも設定されていないスタンプを表示できます。
                </p>

                <p>
                    (3/16) タグ機能を試験的に追加しました。（細部や外観はあとで調整します）<br>
                    各スタンプの左上にある緑のタグアイコンから、スタンプに対してタグを設定できます。スタンプ一覧の左側に並ぶタグ名を選択すると、そのタグが付与されたスタンプを表示できます。
                </p>
            </div>
        </div>

        {{--
        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h3 class="panel-title">お知らせ（2/22）</h3>
                    </div>
                    <div class="panel-body">
                        <p>
                            高負荷対策を行いサーバ状態を監視してきましたが、現状の利用状況であれば問題無さそうなレベルにまで落ち着きました。ご協力ありがとうございました。<br>引き続き負荷対策を実施しつつも、新しい機能の拡充にも手をいれていく予定です。
                        </p>

                        <ul>
                            <li>ページ表示モードはサーバ・ブラウザ側双方に負荷が高かったため修正中。</li>
                            <li>スタンプ一覧の表示限界数を<span style="text-decoration: line-through">300</span>500前後で調整中。</li>
                            <li>連投制限の回数や間隔は随時調整中。</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="panel panel-success">
                    <div class="panel-heading">
                        <h3 class="panel-title">更新情報</h3>
                    </div>

                    <div class="panel-body">
                        <p>
                            (2/25 00:10) 配信者用にスタンプ履歴ページを作成しました。（現状では）直近5分の履歴が参照でき、迷惑なスタンプ送信者はこの画面からブラックリストに追加することができます。
                        </p>

                        <p>
                            (2/18 14:37) 一覧ページの重さ低減のため、アニメgifのサムネイル表示を静止画にして、マウスオーバー中にのみ再生表示するようにしました。アニメgifの場合はサムネイル中央にマークが出ます。<br>
                            スタンプの左下のボタンからオリジナルサイズを表示できるようになりました。スタンプを保存したい時はこちらからどうぞ。<br>
                            <strong>一覧の表示からそのまま保存してしまうと、サムネイルの荒いファイルが保存されてしまいます。</strong>
                        </p>
                    </div>
                </div>

            </div>
        </div>
        --}}

        <div class="room-info">
            <h3><a href="{{ room_path() }}">{{$room->name}}</a>
                <div style="margin-left: 20px; display: inline-block">
                    @switch($room->uploader_level)
                        @case(1)
                        <span class="badge badge-success">追加: 誰でも可能</span>
                        @break
                        @case(2)
                        <span class="badge badge-warning">追加: 会員のみ可能</span>
                        @break
                        @case(9)
                        <span class="badge badge-danger">追加: 全員不可能</span>
                        @break
                    @endswitch
                    @switch($room->imprinter_level)
                        @case(1)
                        <span class="badge badge-success">送信: 誰でも可能</span>
                        @break
                        @case(2)
                        <span class="badge badge-warning">送信: 会員のみ可能</span>
                        @break
                    @endswitch
                </div>

                        <div class="pull-right">
                            <small>
                                @if (
                                ($room->uploader_level === \App\Room::UPLOADER_LEVEL_ANYONE) ||
                                (($room->uploader_level === \App\Room::UPLOADER_LEVEL_USER_ONLY) && auth() && auth()->user())
                                )
                                    @if(session('send.room') && session('send.room.id') === $room->id)
                                        <button class="btn btn-warning btn-sm disabled" role="button">
                                            <i class="fas fa-crosshairs"></i> スタンプ送信先に固定中
                                        </button>
                                    @else
                                    <a class="btn btn-warning btn-sm" href="{{ route('room_target', ['room' => $room->id]) }}" role="button">
                                        <i class="fas fa-crosshairs"></i> ここをスタンプ送信先に固定
                                    </a>
                                    @endif
                                @endif
                                @auth
                                @if( auth()->user()->id === $room->user_id )
                                <a class="btn btn-success btn-sm" href="{{ route('room_imprint', ['room' => $room->id]) }}" role="button">
                                    <i class="far fa-list-alt"></i> スタンプ履歴
                                </a>
                                <a class="btn btn-success btn-sm" href="{{ route('room_edit', ['room' => $room->id]) }}" role="button">
                                    <i class="fas fa-edit"></i> ルーム設定変更
                                </a>
                                @endif
                                @endauth
                            </small>
                        </div>
            </h3>

            @if(strlen($room->description) > 0)
            <div class="room-description">
                {!! nl2br(htmlspecialchars($room->description)) !!}
            </div>
            @endif
        </div>

        <stamp-list
                ref="stampList"
                :room="{id: {{ $room->id }}, userId: {{ $room->user_id }}}"
                :room-id="{{ $room->id }}"
                :uploader-level="{{ $room->uploader_level }}"
                :imprinter-level="{{ $room->imprinter_level }}"
                :init-tag="{{ is_null($tag) ? 'null' : "'" . $tag . "'" }}"
                :no-tags="{{ $noTags }}"
                @auth
                :user-id="{{ auth()->user()->id }}"
                @endauth
                @guest
                :user-id="null"
                @endguest
                @if(session('send.room'))
                :send-room-id="{{ session('send.room.id') }}"
                @endif
        ></stamp-list>

        @if(session('send.room'))
            <div style="position: fixed; bottom: 60px; left:0; right:0;">
                <div style="display: flex; justify-content:center;">
                    <div class="alert alert-danger" style=" background-color: rgba(242, 222, 222, 0.7);">
                        <span style="font-weight: bold; text-shadow:2px 2px 4px #ffffff;">現在、どのルームからスタンプを送信しても「{{ session('send.room.name') }}」(id: {{ session('send.room.id') }}) に届きます。</span> <a class="btn btn-danger btn-sm" href="{{ route('clear_room_target') }}">解除する</a>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@section('footer_script')
    <script src="//www.promisejs.org/polyfills/promise-7.0.4.min.js"></script>
    <script src="{{ mix('js/listener.js') }}"></script>
@endsection