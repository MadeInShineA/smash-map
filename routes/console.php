<?php

use App\Models\Address;
use App\Models\Character;
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

Artisan::command('delete-addresses', function (){
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

Artisan::command('delete-events', function(){
    $events = Event::all();
    $current_time = date('Y-m-d H:i:s');


    foreach ($events as $event){
        if ($event->end_date_time < $current_time){
            $images = $event->images;
            $base_directory_path = base_path(). '/storage/app/public/events-images/event-' . $event->id;
            foreach ($images as $image) {
                $image_directory_path = $base_directory_path . '/' . $image->type;
                unlink($image_directory_path . '/' . $image->uuid);

                $image->delete();
            }
            #TODO Is there a better way to do it ?
            if (file_exists($base_directory_path . '/banner')){
                rmdir($base_directory_path . '/banner');
            }
            if (file_exists($base_directory_path . '/profile')){
                rmdir($base_directory_path . '/profile');
            }
            if (file_exists($base_directory_path)){
                rmdir($base_directory_path);
            }

            var_dump('Images for:' . $event->name . ' deleted');
            var_dump('Event: ' . $event->name . ' deleted');
            $event->delete();
        }
    }
});

Artisan::command('import-events', function(){

//    Artisan::call('delete-events');
//    Artisan::call('delete-addresses');


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
          lat
          lng
          venueAddress
          countryCode
          endAt
          url
          timezone
          images {
            url
            type
          }
          events(filter:{
            videogameId: [$videogameId]
  		    })
            {
            numEntrants
            startAt
            isOnline
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
        $start_gg_updated_at = $start_gg_updated_at->format('Y-m-d H:i:s');

        $melee_event = null;
        if ($event->events){
            $melee_event = $event->events[0];
        }

        if($melee_event){

            $event_object = Event::where('start_gg_id', $start_gg_id)->first();
            if($event_object){
                $event_attendees = $melee_event->numEntrants;
                $event_object->attendees = $event_attendees;
                $event_object->save();
            }
            if(!$event_object || $event_object->start_gg_updated_at < $start_gg_updated_at){
                $is_online = $melee_event->isOnline;
                $name = $event->name;
                $video_game = 'Super Smash Bros. Melee';
                $timezone = $event->timezone;

                $start_date = new DateTime();
                $start_date->format('Y-m-d H:i:s');

                $start_date->setTimestamp($melee_event->startAt);

                $end_date = new DateTime();
                $end_date->format('Y-m-d H:i:s');
                $end_date->setTimestamp($event->endAt);

                $attendees = $melee_event->numEntrants;

                $link = 'https://www.start.gg' . $event->url;

                $event_object = Event::updateOrCreate(['start_gg_id' =>$start_gg_id], ['start_gg_updated_at' =>$start_gg_updated_at, 'is_online' =>$is_online, 'name' =>$name, 'video_game' =>$video_game, 'timezone' =>$timezone, 'start_date_time' =>$start_date, 'end_date_time' =>$end_date, 'attendees' =>$attendees, 'link' =>$link]);
                var_dump('Event: ' . $event->name . ' created');
                if(!$event_object->is_online){
                    $latitude = $event->lat;
                    $longitude = $event->lng;

                    $address_name = $event->venueAddress;
                    $country_code = $event->countryCode;

                    $country = Country::where('code',$country_code)->first();

                    # Handle the Oceania case
                    if($country){
                        $continent = $country->continent;
                    }else{
                        $continent = $event->venueAddress;
                    }

                    $address = $event_object->address;

                    if(!$address){

                        //FIXME Doesn't work if we add the latitude and longitude => php float vs SQL float ?
                        $address = Address::firstOrCreate(['name'=>$address_name, 'country_id' =>$country?->id, 'continent_id' =>$continent->id],['latitude' =>$latitude, 'longitude' =>$longitude]);
                        $event_object->address_id = $address->id;
                        $event_object->save();
                    }
                }

                $event_directory_path = '/events-images/event-' . $event_object->id;

                $event_db_md5s = $event_object->images->pluck('md5')->toArray();

                #TODO Delete the unused images (inside event_db_md5s but not in event_md5s)

//            $event_md5s = [];


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
                #TODO Delete the unused images (inside event_db_md5s but not in event_md5s)

//                $event_md5s[] = $image_md5;

                    if (!in_array($image_md5, $event_db_md5s)) {
                        Storage::put($event_directory_path . '/' . $image_type . '/' . $uuid, $image);
                        Image::Create(['parentable_type' =>Event::class, 'parentable_id' =>$event_object->id, 'type' =>$image_type, 'uuid' => $uuid, 'md5' => $image_md5]);
                    }
                }
                var_dump('Images for:' . $event->name . ' created');

            }

        }

    }

});

Artisan::command('import-characters-images',function(){
    $characters = Character::all();

    foreach ($characters as $character){
        $curl_handle=curl_init();
        curl_setopt($curl_handle, CURLOPT_URL,$character->image_link);
        curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl_handle, CURLOPT_USERAGENT, 'Melee Map');
        $query = curl_exec($curl_handle);

        curl_close($curl_handle);

        $image = $query;
        $uuid = Str::uuid()->toString() . '.png';
        $image_md5 = md5($image);

        Image::Create(['parentable_type' =>Character::class, 'parentable_id' =>$character->id, 'type' =>'character', 'uuid' => $uuid, 'md5' => $image_md5]);

        $character_directory_path = '/characters-images/' . $character->name;
        Storage::put($character_directory_path . '/' . $uuid, $image);
        var_dump('Image for: ' . $character->name . ' created');
    }
});

Artisan::command('import-countries-images', function (){
    $countries = Country::all();

    foreach ($countries as $country){
        $url = 'https://flagsapi.com/' . $country->code .'/flat/64.png';
        $curl_handle=curl_init();
        curl_setopt($curl_handle, CURLOPT_URL,$url);
        curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl_handle, CURLOPT_USERAGENT, 'Melee Map');
        $query = curl_exec($curl_handle);

        $image = $query;
        $uuid = Str::uuid()->toString() . '.png';
        $image_md5 = md5($image);
        curl_close($curl_handle);

        Image::Create(['parentable_type' =>Country::class, 'parentable_id' =>$country->id, 'type' =>'country', 'uuid' => $uuid, 'md5' => $image_md5]);

        $character_directory_path = '/countries-images/' . $country->name;
        Storage::put($character_directory_path . '/' . $uuid, $image);
        var_dump('Image for: ' . $country->name . ' created');
    }

});
