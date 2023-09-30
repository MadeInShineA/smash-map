<?php

use App\Enums\GameEnum;
use App\Models\Address;
use App\Models\Character;
use App\Models\Country;
use App\Models\Event;
use App\Models\Image;
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

Artisan::command('import-100-events {game} {page?}', function(string $game, int $page=1){

    switch ($game){
        case '64':
            $game_id = GameEnum::SMASH64;
            break;
        case 'melee':
            $game_id = GameEnum::MELEE;
            break;
        case 'brawl':
            $game_id = GameEnum::BRAWL;
            break;
        case 'project_+':
            $game_id = GameEnum::PROJECT_PLUS;
            break;
        case 'project_m':
            $game_id = GameEnum::PROJECT_M;
            break;
        case 'smash4':
            $game_id = GameEnum::SMASH4;
            break;
        case 'ultimate':
            $game_id = GameEnum::ULTIMATE;
            break;
        default:
            var_dump('Unknown game');
            die();
    }

    $apiToken = env('START_GG_API_KEY');

    $endpointUrl = 'https://api.start.gg/gql/alpha';
    $query = 'query TournamentsByVideogame($videogameId: ID!, $page: Int!) {
      tournaments(query: {
        sortBy: "startAt asc"
        perPage: 100
        page: $page
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
      'variables' => ['videogameId' => $game_id, 'page'=>$page],
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

        $game_event = null;
        if ($event->events){
            $single_events = array_values(array_filter($event->events, function($event) {
                return $event->type === 1;
                }));

            if ($single_events){
                $game_event = array_reduce($single_events, function ($carry, $event){
                    if ($event->numEntrants > $carry->numEntrants) {
                        return $event;
                    }
                    return $carry;
                }, $single_events[0]);
            }
        }

        if($game_event){

            $event_object = Event::where('start_gg_id', $start_gg_id)->where('game_id', $game_id)->first();
            if($event_object){
                $event_attendees = $game_event->numEntrants;
                $event_object->attendees = $event_attendees;
                $event_object->save();
            }
            if(!$event_object || $event_object->start_gg_updated_at < $start_gg_updated_at){
                $is_online = $game_event->isOnline;
                $name = $event->name;
                $timezone = $event->timezone;
                if(!$timezone){
                    $timezone = 'UTC';
                }

                $start_date = new DateTime();
                $start_date->format('Y-m-d H:i:s');

                $start_date->setTimestamp($game_event->startAt);

                $end_date = new DateTime();
                $end_date->format('Y-m-d H:i:s');
                $end_date->setTimestamp($event->endAt);

                if ($start_date > $end_date){
                    continue;
                }

                $attendees = $game_event->numEntrants;

                $link = 'https://www.start.gg' . $event->url;

                $event_object = Event::updateOrCreate(['start_gg_id' =>$start_gg_id, 'game_id'=>$game_id], ['start_gg_updated_at' =>$start_gg_updated_at, 'is_online' =>$is_online, 'name' =>$name, 'timezone' =>$timezone, 'start_date_time' =>$start_date, 'end_date_time' =>$end_date, 'attendees' =>$attendees, 'link' =>$link]);
                var_dump('Event: ' . $event->name . ' created');
                if(!$event_object->is_online){
                    $latitude = $event->lat;
                    $latitude = round($latitude, 7);

                    $longitude = $event->lng;
                    $longitude = round($longitude, 7);

                    $address_name = $event->venueAddress;
                    $country_code = $event->countryCode;

                    $country = Country::where('code',$country_code)->first();

                    # Handle the Oceania case
                    if($country){
                        $continent = $country->continent;
                    }else{
                        var_dump('Country not found : ' . $country_code . ' for event: ' . $event->name .'with start gg id: ' . $start_gg_id);
                        Log::error('Country not found : ' . $country_code . ' for event: ' . $event->name .'with start gg id: ' . $start_gg_id);
                        die();
                    }

                    $address = $event_object->address;

                    if(!$address){

                        //FIXME Doesn't work if we add the latitude and longitude => php float vs SQL float ?
                        $address = Address::firstOrCreate(['latitude' =>$latitude, 'longitude' =>$longitude],['name'=>$address_name, 'country_id' =>$country?->id, 'continent_id' =>$continent->id]);
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
                    curl_setopt($curl_handle, CURLOPT_USERAGENT, 'Smash Map');
                    $query = curl_exec($curl_handle);

                    curl_close($curl_handle);

                    $image = $query;

                    $image_md5 = md5($image);
                #TODO Delete the unused images (inside event_db_md5s but not in event_md5s)

//                $event_md5s[] = $image_md5;

                    if (!in_array($image_md5, $event_db_md5s)) {
                        Storage::put($event_directory_path . '/' . $image_type . '/' . $uuid, $image);
                        Image::Create(['parentable_type' =>Event::class, 'parentable_id' =>$event_object->id, 'type' =>$image_type, 'uuid' => $uuid, 'md5' => $image_md5]);
                        var_dump('Images for:' . $event->name . ' created');
                    }
                }
            }

        }

    }

});

Artisan::command('import-100-events-all-games', function (){
    var_dump('Starting the 64 import');
    Artisan::call('import-100-events', ['game' => '64']);
    var_dump('Starting the melee import');
    Artisan::call('import-100-events', ['game' => 'melee']);
    var_dump('Starting the melee brawl');
    Artisan::call('import-100-events', ['game' => 'brawl']);
    var_dump('Starting the project + import');
    Artisan::call('import-100-events', ['game' => 'project_+']);
    var_dump('Starting the project m import');
    Artisan::call('import-100-events', ['game' => 'project_m']);
    var_dump('Starting the smash4 import');
    Artisan::call('import-100-events', ['game' => 'smash4']);
    var_dump('Starting the ultimate import');
    Artisan::call('import-100-events', ['game' => 'ultimate']);
});

Artisan::command('import-500-events {game}', function (string $game){
    foreach (range(1, 5) as $page){
            Artisan::call('import-100-events', ['game' => $game, 'page' => $page]);
    }
});

Artisan::command('import-500-events-all-games', function (){
    var_dump('Starting the 64 import');
    Artisan::call('import-500-events', ['game' => '64']);
    var_dump('Starting the melee import');
    Artisan::call('import-500-events', ['game' => 'melee']);
    var_dump('Starting the brawl import');
    Artisan::call('import-500-events', ['game' => 'brawl']);
    var_dump('Starting the project + import');
    Artisan::call('import-500-events', ['game' => 'project_+']);
    var_dump('Starting the project m import');
    Artisan::call('import-500-events', ['game' => 'project_m']);
    var_dump('Starting the smash4 import');
    Artisan::call('import-500-events', ['game' => 'smash4']);
    var_dump('Starting the ultimate import');
    Artisan::call('import-500-events', ['game' => 'ultimate']);
});

Artisan::command('import-characters-images',function(){
    $characters = Character::all();

    foreach ($characters as $character){
        $curl_handle=curl_init();
        curl_setopt($curl_handle, CURLOPT_URL,$character->image_link);
        curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl_handle, CURLOPT_USERAGENT, 'Smash Map');
        $query = curl_exec($curl_handle);

        curl_close($curl_handle);

        $image = $query;
        $uuid = Str::uuid()->toString() . '.png';
        $image_md5 = md5($image);

        Image::Create(['parentable_type' =>Character::class, 'parentable_id' =>$character->id, 'type' =>'character', 'uuid' => $uuid, 'md5' => $image_md5]);

        $game_slug = $character->game->slug;
        $character_directory_path = '/characters-images/' . $game_slug . '/' . $character->name;
        Storage::put($character_directory_path . '/' . $uuid, $image);
        var_dump('Image for: ' . $character->name . ' created');
    }
});

##TODO Add the image for Frencch Guiana
Artisan::command('import-countries-images', function (){
    $countries = Country::all();

    foreach ($countries as $country){

        $url = 'https://flagsapi.com/' . $country->code .'/flat/64.png';
        $curl_handle=curl_init();
        curl_setopt($curl_handle, CURLOPT_URL,$url);
        curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl_handle, CURLOPT_USERAGENT, 'Smash Map');
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

Artisan::command('test-broadcast', function(){
    broadcast(new \App\Events\NotificationEvent(\App\Models\User::first()));
});

Artisan::command('setup', function(){
   Artisan::call('import-countries-images');
   Artisan::call('import-characters-images');
   Artisan::call('import-100-events-all-games');
});
