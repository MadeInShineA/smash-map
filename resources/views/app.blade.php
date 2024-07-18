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

    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-N3ZXHKPL');</script>
    <!-- End Google Tag Manager -->
</head>
<body>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-N3ZXHKPL"
                      height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->

    <div id="app"></div>
    @vite('resources/js/app.js')
    <script>
        if ("serviceWorker" in navigator) {
            // Register a service worker hosted at the root of the
            // site using the default scope.
            navigator.serviceWorker.register("{{ asset('/sw.js') }}").then(
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
