@extends('layouts.app')

@section('title')
    {{ config('app.name', 'StampCast') }} - {{$room->name}}
@endsection

@section('content')
    <div class="container-fluid">

        <div class="panel panel-success">
            <div class="panel-heading">
                <h3 class="panel-title">更新情報</h3>
            </div>

            <div class="panel-body" style="height:126px; overflow-y: scroll;">
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
            <h3>{{$room->name}}
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
                @auth
                    @if( auth()->user()->id === $room->user_id )
                        <div class="pull-right">
                            <small>
                                <a class="btn btn-success btn-sm" href="{{ route('room_imprint', ['room' => $room->id]) }}" role="button">
                                    <i class="far fa-list-alt"></i> スタンプ履歴
                                </a>
                                <a class="btn btn-success btn-sm" href="{{ route('room_edit', ['room' => $room->id]) }}" role="button">
                                    <i class="fas fa-edit"></i> ルーム設定変更
                                </a>
                            </small>
                        </div>
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