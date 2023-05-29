<?php

use App\Models\Address;
use App\Models\Country;
use App\Models\Event;
use App\Models\Image;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;


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

Artisan::command('delete_addresses', function (){
    $addresses = Address::all();
    foreach ($addresses as $address) {
        $event_count = $address->events->count();
        $user_count = $address->users->count();

        if ($event_count === 0 && $user_count === 0){
            var_dump('Address: ' . $address->name . ' deleted');
            $address->delete();
        }
    }
});

Artisan::command('delete_events', function(){
    $events = Event::all();
    $current_time = date('Y-m-d h:i:s');


    foreach ($events as $event){
        if ($event->end_date_time < $current_time){
            $images = $event->images;
            foreach ($images as $image) {
                $event_directory_path = base_path(). '/events_images/event_' . $event->id;
                if(file_exists($event_directory_path . '/' . $image->uuid))
                    unlink($event_directory_path . '/' . $image->uuid);
                $image->delete();
            }
            $event->delete();
        }
    }
});

Artisan::command('import_events', function(){

    Artisan::call('delete_events');


    $video_game_id = '1';
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
          id
          updatedAt
          name
          isOnline
          lat
          lng
          venueAddress
          countryCode
          startAt
          endAt
          url
          timezone
          images {
            url
            type
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
      'variables' => ['videogameId' => $video_game_id],
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

    foreach ($events as $event){

        $start_gg_id = $event->id;
        $start_gg_updated_at = new DateTime();
        $start_gg_updated_at->setTimestamp($event->updatedAt);
        $start_gg_updated_at = $start_gg_updated_at->format('Y-m-d h:i:s');


        $event_object = Event::where('start_gg_id', $start_gg_id)->first();

        if(!$event_object || $event_object->start_gg_updated_at < $start_gg_updated_at){
            $is_online = $event->isOnline;
            $name = $event->name;
            $video_game = 'Super Smash Bros. Melee';
            $timezone = $event->timezone;

            $start_date = new DateTime();
            $start_date->format('Y-m-d h:i:s');
            $start_date->setTimestamp($event->startAt);

            $end_date = new DateTime();
            $end_date->format('Y-m-d h:i:s');
            $end_date->setTimestamp($event->endAt);

            $link = 'https://www.start.gg' . $event->url;

            $event_object = Event::updateOrCreate(['start_gg_id' =>$start_gg_id], ['start_gg_updated_at' =>$start_gg_updated_at, 'is_online' =>$is_online, 'name' =>$name, 'video_game' =>$video_game, 'timezone' =>$timezone, 'start_date_time' =>$start_date, 'end_date_time' =>$end_date, 'link' =>$link]);

            if(!$event_object->is_online){
                $latitude = $event->lat;
                $longitude = $event->lng;

                $address_name = $event->venueAddress;
                $country_code = $event->countryCode;

                $country = Country::where('code',$country_code)->first();

                $address = $event_object->address;

//                1	2023-05-29 20:18:44	2023-05-29 20:18:44	15	82 Church St, Wollongong NSW 2500, Australia	-34.43	150.89
//
//                84	2023-05-29 20:44:58	2023-05-29 20:44:58	15	82 Church St, Wollongong NSW 2500, Australia	-34.43	150.89
                if(!$address){

                    //FIXME Doesn't work if we add the latitude and longitude => php float vs SQL float ?
                    $address = Address::firstOrCreate(['name'=>$address_name, 'country_id' =>$country->id]);
                    $event_object->address_id = $address->id;
                    $event_object->save();
                }
            }

            $event_directory_path = '/events_images/event_' . $event_object->id;

            $event_db_md5s = $event_object->images->pluck('md5')->toArray();

            #TODO Delete the unused images (inside event_db_md5s but not in event_md5s)
            $event_md5s = [];


            $images = $event->images;

            foreach ($images as $image) {

                $uuid = Str::uuid()->toString() . '.jpg';

                $image_type = $image->type;
                $curl_handle=curl_init();
                curl_setopt($curl_handle, CURLOPT_URL,$image->url);
                curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
                curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curl_handle, CURLOPT_USERAGENT, 'Melee Map');
                $query = curl_exec($curl_handle);

                curl_close($curl_handle);

                $image = $query;

                $image_md5 = md5($image);

                $event_md5s[] = $image_md5;

                if (!in_array($image_md5, $event_db_md5s)) {
                    Storage::put($event_directory_path . '/' . $image_type . '/' . $uuid, $image);
                    Image::Create(['parentable_type' =>'App\Models\Event', 'parentable_id' =>$event_object->id, 'type' =>$image_type, 'uuid' => $uuid, 'md5' => $image_md5]);
                    }
                }
        }
    }

});
