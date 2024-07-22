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
    <link rel="apple-touch-icon" href="{{ asset('/images/logo-512.png') }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Smash Map(Dev)">
    <link rel="manifest" href="{{ asset('/manifest.json') }}">

    <meta name="title" content="Smash Map">
    <meta name="description" content="Smash Map is an open-source website listing competitive Super Smash Bros. events. Discover events, find players, create profiles, and receive notifications.">
    <meta name="keywords" content="Smash Map, Super Smash Bros, competitive gaming, esports, events, start.gg, game statistics, player profiles, notifications">
    <meta name="author" content="MadeInShineA">
</head>
<body>
    <div id="app"></div>
    @vite('resources/js/app.js')
    <script>
        if ("serviceWorker" in navigator) {
            // Register a service worker hosted at the root of the
            // site using the default scope.
            navigator.serviceWorker.register("{{ asset('/sw.js') }}").then(
                (registration) => {
                    registration.onupdatefound = () => {
                        const installingWorker = registration.installing;
                        installingWorker.onstatechange = () => {
                            if (installingWorker.state === 'installed') {
                                if (navigator.serviceWorker.controller) {
                                    // New update available
                                    console.log('New or updated content is available.');
                                    window.location.reload();
                                    // Notify user to refresh the page
                                } else {
                                    // Content is cached for offline use
                                    console.log('Content is now available offline!');
                                }
                            }
                        };
                    };
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
