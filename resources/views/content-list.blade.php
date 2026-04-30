@extends('factotum_cms::layouts.app')

@section('content')

    @if(isset($contentList))
        @foreach ($contentList as $content)
            <article>
                <h1>{{ $content->title }}</h1>
            </article>
        @endforeach
    @endif

@endsection
