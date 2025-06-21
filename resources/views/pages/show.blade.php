@extends('layouts.public')

@section('content')
    <div>
        @foreach($page->blocks as $block)
            @renderBlock($block)
        @endforeach
    </div>
@endsection
