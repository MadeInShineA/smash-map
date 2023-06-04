<!DOCTYPE html>
<html>
<head>
    <title>Melee Map</title>
    <link rel="icon" type="image/x-icon" href="{{@asset('images/logo-no-text-white-globe.png')}}">
    <link id="theme-link" rel="stylesheet" href="{{@asset('themes/bootstrap4-light-blue/theme.css')}}">
{{--    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">--}}
    @vite('resources/css/app.css')
</head>
<body>
    <div id="app"></div>
    @vite('resources/js/app.js')

</body>
</html>
