@extends('layouts.app')

@section('content')
    @foreach($page->blocks as $block)
        @livewire($block['block']['class'], $block['parameter_values'] ?? [], $block['reference'])
    @endforeach
@endsection
