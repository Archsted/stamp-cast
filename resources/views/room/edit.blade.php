@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">ルーム修正</div>

                    <div class="panel-body">
                        <form class="form-horizontal" method="POST" action="{{ route('room_update', ['room' => $room->id]) }}">

                            {{ csrf_field() }}

                            {{ method_field('put') }}

                            <p>ルーム設定を変更できます。</p>

                            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                <label for="name" class="col-md-3 control-label">ルーム名</label>

                                <div class="col-md-6">
                                    <input id="name" type="text" class="form-control" name="name" value="{{ old('name', $room->name) }}" placeholder="会長ch" required>

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
                                    <textarea name="description" id="description" class="form-control" cols="30" rows="4" placeholder="配信画面にスタンプを送る事ができます。">{{ old('description', $room->description) }}</textarea>
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

                            <div class="form-group{{ $errors->has('imprinter_level') ? ' has-error' : '' }}">
                                <label for="description" class="col-md-3 control-label">スタンプを送ることが<br>可能なユーザー</label>

                                <div class="col-md-6">
                                    <label class="radio-inline">
                                        <input type="radio" name="imprinter_level" id="imprinter_level_1" value="1"
                                        @if($room->imprinter_level === 1)
                                            checked
                                        @endif
                                        > 誰でも可能
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="imprinter_level" id="imprinter_level_2" value="2"
                                        @if($room->imprinter_level === 2)
                                            checked
                                        @endif
                                        > 会員のみ可能
                                    </label>

                                    @if ($errors->has('imprinter_level'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('imprinter_level') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('uploader_level') ? ' has-error' : '' }}">
                                <label for="description" class="col-md-3 control-label">スタンプを新規で<br>アップロード可能なユーザー</label>

                                <div class="col-md-6">
                                    <label class="radio-inline">
                                        <input type="radio" name="uploader_level" id="uploader_level_1" value="1"
                                               @if($room->uploader_level === 1)
                                               checked
                                                @endif
                                        > 誰でも可能
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="uploader_level" id="uploader_level_2" value="2"
                                               @if($room->uploader_level === 2)
                                               checked
                                                @endif
                                        > 会員のみ可能
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="uploader_level" id="uploader_level_9" value="9"
                                               @if($room->uploader_level === 9)
                                               checked
                                                @endif
                                        > 全員不可能
                                    </label>

                                    @if ($errors->has('uploader_level'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('uploader_level') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-3">
                                    <button type="submit" class="btn btn-primary">
                                        修正
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
