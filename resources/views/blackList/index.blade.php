@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>ブラックリスト管理</h1>

        <p>ブラックリストへの追加が新しい順に表示しています。削除ボタンを押すことでブラックリストから削除できます。</p>

        <table class="table table-hover">
            <thead>
            <tr>
                <th>管理ID</th>
                <th>リストに追加した日時</th>
                <th>IPアドレス(一部のみ表示)</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($blackListIps as $blackListIp)
            <tr>
                <td>{{ $blackListIp->id }}</td>
                <td>{{ $blackListIp->created_display }}</td>
                <td>{{ $blackListIp->masked_ip }}</td>
                <td>
                    <button class="btn btn-danger btn-sm" onclick="del({{ $blackListIp->id }})"><i class="fas fa-times-circle"></i> 削除</button>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>

    </div>
@endsection

@section('footer_script')
    <script src="{{ mix('js/app.js') }}"></script>

    <script>
        function del(id) {
            if (window.confirm('管理ID：' + id + '\nこのブラックリストを削除しますか？')) {
                axios.delete('/api/v1/blackLists/' + id)
                    .then( function (response) {
                        location.reload();
                    })
                    .catch( function (error) {
                        alert('削除に失敗しました。');
                    });
            }
        }
    </script>

@endsection