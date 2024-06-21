<!DOCTYPE html>
<html>
<head>
    <title>Smash Map</title>
    <link rel="icon" type="image/x-icon" href="{{@asset('images/transparent-logo.png')}}">
    <link id="theme-link" rel="stylesheet" href="{{@asset('themes/mdc-dark-indigo/theme.css')}}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite('resources/css/app.css')

    <!-- PWA  -->
    <meta name="theme-color" content="#ffffff"/>
    <link rel="apple-touch-icon" href="{{ asset('/images/logo.png') }}">
    <link rel="manifest" href="{{ asset('/manifest.json') }}">
</head>
<body>
    <div id="app"></div>
    @vite('resources/js/app.js')

    <script src="{{ asset('/sw.js') }}"></script>
    <script>
        if ("serviceWorker" in navigator) {
            // Register a service worker hosted at the root of the
            // site using the default scope.
            navigator.serviceWorker.register("/sw.js").then(
                (registration) => {
                    console.log("Service worker registration succeeded:", registration);
                },
                (error) => {
                    console.error(`Service worker registration failed: ${error}`);
                },
            );
        } else {
            console.error("Service workers are not supported.");
        }
    </script>
</body>
</html>

<style>
    body{
        font-family: Roboto, Helvetica Neue Light, Helvetica Neue, Helvetica, Arial, Lucida Grande, sans-serif;
    }
</style>
