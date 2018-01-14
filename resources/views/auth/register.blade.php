@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">新規登録</div>

                <div class="panel-body">
                    <form class="form-horizontal" method="POST" action="{{ route('register') }}">

                        <p>全て入力必須。ログインIDとパスワードは非公開情報です。</p>

                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-3 control-label">ユーザー名</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" placeholder="会長" required>

                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <div class="col-md-3">
                                <span class="help-block">
                                    任意の文字列
                                </span>
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('login_id') ? ' has-error' : '' }}">
                            <label for="login_id" class="col-md-3 control-label">ログインID</label>

                            <div class="col-md-6">
                                <input id="login_id" type="text" class="form-control" name="login_id" value="{{ old('login_id') }}" placeholder="archsted" required autofocus>
                                @if ($errors->has('login_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('login_id') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <div class="col-md-3">
                                <span class="help-block">
                                    半角の英数字記号
                                </span>
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-3 control-label">パスワード</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <div class="col-md-3">
                                <span class="help-block">
                                    6文字以上
                                </span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password-confirm" class="col-md-3 control-label">確認用</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                            </div>

                            <div class="col-md-3">
                                <span class="help-block">
                                    もう一度
                                </span>
                            </div>
                        </div>

                        {{--
                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-3 control-label">メールアドレス</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}">

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <div class="col-md-3">
                                <span class="help-block">
                                    もう一度
                                </span>
                            </div>
                        </div>
                        --}}

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
