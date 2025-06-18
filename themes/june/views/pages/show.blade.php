@extends('layouts.public')

@section('content')
    <div>
        @foreach($page->blocks as $block)
            @livewire($block['block']['class'], $block['parameter_values'] ?? [], $block['reference'])
        @endforeach
    </div>
@endsection
