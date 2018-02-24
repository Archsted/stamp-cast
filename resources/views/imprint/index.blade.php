@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>直近 {{ env('IMPRINTS_LOG_MINUTE', 5) }} 分間のスタンプ送信</h1>

        <p>
            送信者ごとに送信されたスタンプと回数が表示されています。<br>
            ブラックリストに追加するボタンを押下で、送信者をブラックリストに追加できます。
        </p>

        <stamp-imprint :imprints="{{ json_encode($list) }}"></stamp-imprint>
    </div>
@endsection

@section('footer_script')
    <script src="{{ mix('js/imprint.js') }}"></script>
@endsection