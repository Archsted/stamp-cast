@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">ルーム作成</div>

                    <div class="panel-body">
                        <form class="form-horizontal" method="POST" action="{{ route('room_store') }}">

                            {{ csrf_field() }}

                            <p>ここの設定は、リスナーがアクセスするスタンプ送信画面の上部に表示される情報です。</p>
                            <p>あとからいつでも変更可能です。</p>

                            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                <label for="name" class="col-md-3 control-label">ルーム名</label>

                                <div class="col-md-6">
                                    <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" placeholder="会長ch" required>

                                    @if ($errors->has('name'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('name') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                                <label for="description" class="col-md-3 control-label">備考</label>

                                <div class="col-md-6">
                                    <textarea name="description" id="description" class="form-control" cols="30" rows="4" placeholder="配信画面にスタンプを送る事ができます。">{{ old('description') }}</textarea>
                                    <span class="help-block">
                                        任意
                                    </span>
                                    @if ($errors->has('description'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('description') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-3">
                                    <button type="submit" class="btn btn-primary">
                                        新規登録
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
