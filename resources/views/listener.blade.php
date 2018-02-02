@extends('layouts.app')

@section('content')
    <div style="padding: 0 10px;">
        <div>
            <p>
                {{$room->name}}
                @auth
                    @if( auth()->user()->id === $room->user_id )
                        <a href="{{ route('room_edit', ['room' => $room->id]) }}"><span class="glyphicon glyphicon-pencil"></span></a>
                    @endif
                @endauth
            </p>
            <p>
                {!! nl2br(htmlspecialchars($room->description)) !!}
            </p>
        </div>

        <stamp-list
            ref="stampList"
            :room="{id: {{$room->id}}, userId: {{ $room->user_id }}}"
            :room-id="{{ $room->id }}"
            :uploader-level="{{ $room->uploader_level }}"
            :imprinter-level="{{ $room->imprinter_level }}"
            @auth
            :user-id="{{ auth()->user()->id }}"
            @endauth
            @guest
            :user-id="null"
            @endguest
        ></stamp-list>
    </div>
@endsection

@section('footer_script')
    <script src="{{ mix('js/listener.js') }}"></script>
@endsection