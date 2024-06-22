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
    <link rel="manifest" href="{{ asset('/manifest.json') }}">

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

    <!-- Pusher Beam (Push notifications)  -->
    <script src="https://js.pusher.com/beams/1.0/push-notifications-cdn.js"></script>


    <script>
        const beamsClient = new PusherPushNotifications.Client({
            instanceId: '{{ env('PUSHER_BEAMS_INSTANCE_ID') }}',
        });

        beamsClient.start()
            .then(() => beamsClient.addDeviceInterest('hello'))
            .then(() => console.log('Successfully registered and subscribed!'))
            .catch(console.error);
    </script>


</body>
</html>

<style>
    body{
        font-family: Roboto, Helvetica Neue Light, Helvetica Neue, Helvetica, Arial, Lucida Grande, sans-serif;
    }
</style>
