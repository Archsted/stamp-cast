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

            <div class="panel-body">
                <p>
                    (3/16 09:00) タグ機能を試験的に追加しました。（細部や外観はあとで調整します）<br>
                    各スタンプの左上にある緑のタグアイコンから、スタンプに対してタグを設定できます。<br>
                    スタンプ一覧の左側に並ぶタグ名を選択すると、そのタグが付与されたスタンプを表示できます。
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