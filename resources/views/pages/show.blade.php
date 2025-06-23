@extends('layouts.public')

@section('content')
    <div>
        @forelse ($page->blocks as $block)
            @renderBlock($block)
        @empty
        @endforelse
    </div>
@endsection
