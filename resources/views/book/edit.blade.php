@extends('layouts.app')

@section('content')
    <div class="container">
        <ol class="breadcrumb">
            <li><a href="/home"><i class="fas fa-home"></i></a></li>
            <li><a href="/books">スタンプ帳</a></li>
            <li><a href="{{ route('book_show', ['book' => $book->id]) }}">{{ $book->name }}</a></li>
            <li class="active">修正</li>
        </ol>

        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">スタンプ帳 修正</div>

                    <div class="panel-body">
                        <form class="form-horizontal" method="POST" action="{{ route('book_update', ['book' => $book->id]) }}">

                            {{ method_field('PUT') }}

                            {{ csrf_field() }}

                            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                <label for="name" class="col-md-3 control-label">名前</label>

                                <div class="col-md-6">
                                    <input id="name" type="text" class="form-control" name="name" value="{{ old('name', $book->name) }}" placeholder="例）よく使うもの" required autofocus>
                                    <span class="help-block">
                                        入力必須 32文字まで
                                    </span>

                                    @if ($errors->has('name'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('name') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                                <label for="description" class="col-md-3 control-label">メモ</label>

                                <div class="col-md-6">
                                    <input id="description" type="text" class="form-control" name="description" value="{{ old('description', $book->description) }}" placeholder="">
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
