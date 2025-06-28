@extends('layouts.public')

@section('content')
    <div>
        @forelse ($page->blocks as $block)
            @renderBlock($block, $page)
        @empty
        @endforelse
    </div>
@endsection
