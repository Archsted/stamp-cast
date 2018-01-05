@extends('layouts.app')

@section('content')
    <div style="padding: 0 10px;">
        <stamp-list ref="stampList" room-id="{{ $room->id }}"></stamp-list>
    </div>
@endsection

@section('footer_script')
    <script src="{{ asset('js/listener.js') }}"></script>
@endsection