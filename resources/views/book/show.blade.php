@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <book-show :book="{{ $book }}" :can-send-twitter="{{ $canSendTwitter ? 'true' : 'false' }}"></book-show>
    </div>
@endsection

@section('footer_script')
    <script src="{{ mix('js/bookList.js') }}"></script>
@endsection