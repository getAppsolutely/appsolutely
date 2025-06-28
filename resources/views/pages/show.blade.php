@extends('layouts.public')

@section('content')
    <div>
        @forelse ($page->blocks as $block)
            @renderBlock($block, $page->toArray()??[])
        @empty
        @endforelse
    </div>
@endsection
