@extends('layouts.app')

@section('content')
    @foreach($page->blocks as $block)
        @livewire($block['block']['class'], $block['schema_values'] ?? [], $block['reference'])
    @endforeach
@endsection
