@extends('layouts.public')

@section('content')
    <div>
        @foreach($page->blocks as $block)
            @livewire($block['block']['class'], json_decode($block['schema_values'], true) ?? [], $block['reference'])
        @endforeach
    </div>
@endsection
