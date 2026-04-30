@extends('factotum_cms::layouts.app')

@section('content')
	<h1>{{ $content->title }}</h1>
	@isset ($content->news_subtitle)<h2>{{ $content->news_subtitle }}</h2>@endisset
	<time datetime="{{ $content->created_at->toIso8601String() }}">{{ $content->created_at->isoFormat('LL') }}</time>

	<div>{!! $content->content !!}</div>
@endsection
