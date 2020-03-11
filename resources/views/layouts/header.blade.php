<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="app-url" content="{{ config('app.url') }}">

    <title>{{ config('app.name') }}</title>

    <link rel="shortcut icon" href="{{ config('settings.favicon') }}" type="image/x-icon">
	<link rel="stylesheet" href="{{ asset('assets/css/app.css') }}"> 

    <style type="text/css">
	   {{ config('settings.custom_css') }}
    </style>
	
	<?= config('settings.analytics_code'); ?>

</head>
<body>