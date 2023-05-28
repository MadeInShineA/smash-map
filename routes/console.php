<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('import_events', function(){

    $videogameId = '1';
    $apiToken = env('START_GG_API_KEY');

    $endpointUrl = 'https://api.start.gg/gql/alpha';

    $query = 'query TournamentsByVideogame($videogameId: ID!) {
      tournaments(query: {
        sortBy: "startAt asc"
        perPage: 100
        page: 1
        filter: {
          upcoming: true
          videogameIds: [$videogameId]
        }
      }) {
        nodes {
          name
          isOnline
          lat
          lng
          countryCode
          startAt
          endAt
          url
          timezone
          images {
            url
          }

        }
      }
    }';

    $headers = [
      'Content-Type: application/json',
      'Authorization: Bearer ' . $apiToken,
    ];

    $data = [
      'query' => $query,
      'variables' => ['videogameId' => $videogameId],
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_URL, $endpointUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = json_decode(curl_exec($ch));

    if (curl_errno($ch)) {
      echo 'Error: ' . curl_error($ch);
    }

    curl_close($ch);

    $events = $response->data->tournaments->nodes;

    var_dump($events);

    foreach ($events as $event){

        $latitude = $event->lat;
        $longitude = $event->lng;


    }
});
