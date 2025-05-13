@extends('layouts.app')

@section('title', $page->name)

@section('content')
    @foreach($page->containers as $container)
        <div class="container {{ $container->layout }}" style="{{ $container->style }}">
            {!! $container->html !!}

            @foreach($container->components as $component)
                {!! $pageService->renderComponent($component) !!}
            @endforeach
        </div>
    @endforeach
@endsection