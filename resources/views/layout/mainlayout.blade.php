<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.png">
    <title>Preclinic - Medical & Hospital - Bootstrap 5 Admin Template</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    @include('layout.partials.head')
</head>

@if (!Route::is(['error-404', 'error-500']))

    <body>
@endif
@if (Route::is(['error-404', 'error-500']))

    <body class="error-pages">
@endif
@if (!Route::is(['change-password2', 'confirm-mail','error-404','error-500','forgot-password','login','lock-screen','register']))
    <div class="main-wrapper">
@endif
@if (Route::is(['change-password2', 'confirm-mail','forgot-password','login','lock-screen','register']))
    <div class="main-wrapper login-body">
@endif
@if(Route::is(['error-404','error-500']))
<div class="main-wrapper error-wrapper">
    @endif
    @if (!Route::is(['change-password2', 'confirm-mail','forgot-password','login','lock-screen','register','error-404','error-500']))
        @include('layout.partials.header')
        @include('layout.partials.sidebar')
    @endif
    @yield('content')
</div>
@component('components.modal-popup')
@endcomponent
<div class="sidebar-overlay" data-reff=""></div>
@include('layout.partials.footer-scripts')
</body>

</html>
