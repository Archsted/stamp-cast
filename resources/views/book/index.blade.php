@extends('layouts.app')

@section('content')
    <div class="container">

        <ol class="breadcrumb">
            <li><a href="/home"><i class="fas fa-home"></i></a></li>
            <li class="active">スタンプ帳</li>
        </ol>

        <div class="panel panel-default">
            <div class="panel-body">
                スタンプ帳は、お気に入りやよく使うスタンプを追加して、自分専用のスタンプ一覧を作る事ができる機能です。<br>
                スタンプ帳に登録したスタンプは、別のルームにもアップロード不要でそのまま送信できます。
            </div>
        </div>

        <p><a class="btn btn-primary" href="{{ route('book_create') }}"><i class="fas fa-plus-circle"></i> 新規作成</a></p>

        <book-list :books="{{ $books }}"></book-list>
    </div>
@endsection

@section('footer_script')
    <script src="{{ mix('js/bookList.js') }}"></script>
@endsection