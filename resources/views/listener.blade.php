@extends('layouts.app')

@section('title')
    {{ config('app.name', 'StampCast') }} - {{$room->name}}
@endsection

@section('content')
    <div class="container-fluid">

        <div class="panel panel-info">
            <div class="panel-heading">
                <h3 class="panel-title">お知らせ（2/22）</h3>
            </div>
            <div class="panel-body">
                <p>
                    高負荷対策を行いサーバ状態を監視してきましたが、現状の利用状況であれば問題無さそうなレベルにまで落ち着きました。ご協力ありがとうございました。<br>引き続き負荷対策を実施しつつも、新しい機能の拡充にも手をいれていく予定です。
                </p>
                <p>
                    ■現在の制限や調整項目<br>
                    ・以前存在した「ページ表示モード」はサーバ・ブラウザ側双方に負荷が高かったため、作り直すまで利用不可にしています。<br>
                    ・スタンプ一覧の表示限界数を300前後で調整中です。古いものが見えなくなる件は別機能の実装で少し緩和されると思うのでお待ち下さい。<br>
                    ・連投制限の回数や間隔を調整中です。主に、規制されるまで連投をする事が目的化しているユーザが増えるほど、他利用者にも影響が出て来るため厳しい設定になります。
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