@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>スタンプ一覧</h1>

        @foreach($stamps as $stamp)
        <div class="thumbnail stamp_thumbnail">
            <img src="{{ $stamp->name }}" alt="" style="max-height: 130px;">
            <div class="caption">
                <p>{{ $stamp->room->name }} / {{ $stamp->created_at }}</p>
                <p><button class="btn btn-danger">削除</button></p>
            </div>
        </div>
        @endforeach


{{--
        <table class="table table-striped table-condensed">
            <thead>
            <tr>
                <th>画像</th>
                <th>投稿したルーム名</th>
                <th>投稿時間</th>
                <th>削除</th>
            </tr>
            </thead>
            <tbody>
            @foreach($stamps as $stamp)
                <tr>
                    <td><img src="{{ $stamp->name }}" alt="" class="thumbnail" style="max-height: 130px;"></td>
                    <td>{{ $stamp->room->name }}</td>
                    <td>{{ $stamp->created_at }}</td>
                    <td>
                        <button class="btn btn-danger">削除</button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
--}}


    </div>
@endsection

@section('footer_script')
    <script src="{{ mix('js/app.js') }}"></script>
@endsection