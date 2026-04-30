<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>

@include('factotum_cms::layouts.components.header')

<main id="main-content">
	@yield('content')
</main>

@include('factotum_cms::layouts.components.footer')

</body>
</html>
