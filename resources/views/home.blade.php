@extends('layouts.app')

@section('content')
<div class="container">
    @if (session('status'))
    <div class="alert alert-success">
        {{ session('status') }}
    </div>
    @endif

    <div class="alert alert-warning" role="alert">
        現在β版につき、リンクが貼られていない機能はまだ利用できません。（順次作成していく予定です）
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">配信者向け</h3>
                </div>
                <div class="panel-body">
                    <div class="menu-container" style="display: flex;flex-wrap: wrap;">
                        @if($room)
                            <div>
                                <h3><a href="{{ route('listener', ['room' => $room->id]) }}"><i class="fas fa-laptop fa-fw"></i> ルームに移動</a></h3>
                                <p>自分のスタンプルームに移動します。</p>
                            </div>
                            <div>
                                <h3><a href="{{ route('room_edit', ['room' => $room->id]) }}"><i class="fas fa-edit fa-fw"></i> ルーム設定の変更</a></h3>
                                <p>自分のスタンプルームの設定を変更することができます。</p>
                            </div>
                        @else
                            <div>
                                <h3><a href="{{ route('room_create') }}"><i class="fas fa-laptop fa-fw"></i> スタンプルームの作成</a></h3>
                                <p>自分専用のスタンプ送信ページを作ります。ここで作ったルームのURLをリスナーに伝えることで、
                                    スタンプを受け取れるようになります。</p>
                            </div>
                        @endif
                        <div>
                            <h3><a href="{{ route('tool_top') }}"><i class="far fa-object-group fa-fw"></i> スタンプ表示ツール</a></h3>
                            <p>デスクトップにスタンプを表示するためのツールの説明、実行ファイルのダウンロードができます。</p>
                        </div>
                        <div>
                            <h3><i class="fas fa-ban fa-fw"></i> ブラックリスト管理</h3>
                            <p>ブラックリストに追加したユーザーやIPアドレスの管理ができます。</p>
                        </div>
                        <div>
                            <h3><i class="fas fa-book fa-fw"></i> マニュアル</h3>
                            <p>配信者向けの使い方を表示します。</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="panel panel-danger">
                <div class="panel-heading">
                    <h3 class="panel-title">リスナー向け</h3>
                </div>
                <div class="panel-body">
                    <div class="menu-container" style="display: flex;flex-wrap: wrap;">
                        <div>
                            <h3><i class="fas fa-images"></i> スタンプ管理</h3>
                            <p>自分が投稿したスタンプや、お気に入りに設定したスタンプを管理できます。</p>
                        </div>
                        <div>
                            <h3><i class="fas fa-book"></i> マニュアル</h3>
                            <p>リスナー向けの使い方を表示します。</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
