@extends('layouts.public')

@section('content')
    <div>
        @foreach($page->blocks as $block)
            @livewire($block['block']['class'], $block['schema_values'] ?? [], $block['reference'])
        @endforeach
    </div>
@endsection
