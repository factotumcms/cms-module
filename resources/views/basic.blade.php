@extends('factotum_cms::layouts.app')

@section('content')
    <h1>{{ $page->title }}</h1>
    {!! $page->content !!}
@endsection
