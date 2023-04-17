<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title') | {{ env('APP_NAME', 'CSP DOORLOCK') }}</title>
    <!-- Favicon icon -->
    <link rel="shortcut icon" href="{{ asset('dist/images/logo-csp-birumerah.gif') }}" type="image/x-icon">
    <!-- Custom Stylesheet -->
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
    @stack('css')
</head>

<body>
