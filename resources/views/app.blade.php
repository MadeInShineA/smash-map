<!DOCTYPE html>
<html>
<head>
    <title>Smash Map</title>
    <link rel="icon" type="image/x-icon" href="{{@asset('images/logo-no-text-white-globe.png')}}">
    <link id="theme-link" rel="stylesheet" href="{{@asset('themes/mdc-dark-indigo/theme.css')}}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite('resources/css/app.css')
</head>
<body>
    <div id="app"></div>
    @vite('resources/js/app.js')
</body>
</html>

<style>
    body{
        font-family: Roboto, Helvetica Neue Light, Helvetica Neue, Helvetica, Arial, Lucida Grande, sans-serif;
    }
</style>
