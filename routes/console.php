<?php

use App\Enums\GameEnum;
use App\Enums\ImageTypeEnum;
use App\Models\Address;
use App\Models\Character;
use App\Models\Country;
use App\Models\Event;
use App\Models\Image;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
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

// TODO Test correctly
Artisan::command('delete-events', function(){
    $events = Event::all();
    $current_time = date('Y-m-d H:i:s');
    foreach ($events as $event){
        if ($event->end_date_time < $current_time){
            $images = $event->images;
            $base_directory_path = base_path(). '/storage/app/public/events-images/' . Str::slug($event->name);
            foreach ($images as $image) {
                var_dump($image->id);
                $image_directory_path = $base_directory_path . '/' . $image->type .'.png';
                if(file_exists($image_directory_path)){
                    unlink($image_directory_path);
                }
                $image->delete();
            }
            #TODO Is there a better way to do it ?
            if (file_exists($base_directory_path . '/' . ImageTypeEnum::EVENT_PROFILE)){
                rmdir($base_directory_path . '/' . ImageTypeEnum::EVENT_PROFILE);
            }
            if (file_exists($base_directory_path . '/' . ImageTypeEnum::EVENT_BANNER)){
                rmdir($base_directory_path . '/' . ImageTypeEnum::EVENT_BANNER);
            }
            if (file_exists($base_directory_path)){
                rmdir($base_directory_path);
            }

            var_dump('Images for:' . $event->name . ' deleted');
            $event->delete();
            var_dump('Event: ' . $event->name . ' deleted');
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

//    while(!$response->data){
//        $response = json_decode(curl_exec($ch));
//
//        if (curl_errno($ch)) {
//            echo 'Error: ' . curl_error($ch);
//        }
//    }

    curl_close($ch);
    $events = $response?->data->tournaments->nodes;

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
                    if(!$country){
                        var_dump('Country not found : ' . $country_code . ' for event: ' . $event->name .' with start gg id: ' . $start_gg_id);
                        Log::error('Country not found : ' . $country_code . ' for event: ' . $event->name .' with start gg id: ' . $start_gg_id);
                        die();
                    }

                    $address = $event_object->address;

                    if(!$address){

                        //FIXME Doesn't work if we add the latitude and longitude => php float vs SQL float ?
                        $address = Address::firstOrCreate(['latitude' =>$latitude, 'longitude' =>$longitude],['name'=>$address_name, 'country_id' =>$country?->id]);
                        $event_object->address_id = $address->id;
                        $event_object->save();
                    }
                }

                $event_directory_path = '/events-images/' . Str::slug($event->name);

                $event_db_md5s = $event_object->images->pluck('md5')->toArray();

                #TODO Delete the unused images (inside event_db_md5s but not in event_md5s)

//            $event_md5s = [];


                $images = $event->images;

                foreach ($images as $image) {
                    $image_type = $image->type == 'profile'? ImageTypeEnum::EVENT_PROFILE : ImageTypeEnum::EVENT_BANNER;

                    $image_file = file_get_contents($image->url);
                    if (!$image_file){
                        var_dump("Event" . $event->name . " : Image ". $image->url . " couldn't be retrieved");
                        Log::error("Event" . $event->name . " : Image ". $image->url . " couldn't be retrieved");
                        die();
                    }else{
                        $image_md5 = md5($image_file);
                        $isImageStored = Storage::put($event_directory_path . '/' . $image_type . '.png', $image_file);
                        if ($isImageStored){
                            Image::Create(['parentable_type' =>Event::class, 'parentable_id' =>$event_object->id, 'type' =>$image_type,'md5' => $image_md5]);
                            var_dump('Images for:' . $event->name . ' created');
                        }else{
                            var_dump("Image ". $image->url . " couldn't be stored stored");
                            Log::error("Image ". $image->url . " couldn't be stored stored");
                            die();
                        }
                    }
                #TODO Delete the unused images (inside event_db_md5s but not in event_md5s)

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
        Image::Create(['parentable_type' =>Character::class, 'parentable_id' =>$character->id, 'type' => ImageTypeEnum::ICON]);
        var_dump('Image for: ' . $character->name . ' created');
    }
});

##TODO Add the image for Frencch Guiana
Artisan::command('import-countries-images', function (){

//    $url = 'https://flagsapi.com/' . $country->code .'/flat/64.png';
//
//    $image = file_get_contents($url);
//    $uuid = Str::uuid()->toString() . '.png';
//    $image_md5 = md5($image);
//
//    Image::Create(['parentable_type' =>Country::class, 'parentable_id' =>$country->id, 'type' => ImageTypeEnum::ICON, 'uuid' => $uuid, 'md5' => $image_md5]);

    $countries = Country::where('has_image', true)->get();

    foreach ($countries as $country){
        Image::Create(['parentable_type' =>Country::class, 'parentable_id' =>$country->id, 'type' => ImageTypeEnum::ICON]);
        var_dump('Image for: ' . $country->name . ' created');
    }

});

Artisan::command('test-broadcast', function(){
    broadcast(new \App\Events\NotificationEvent(\App\Models\User::where('username', 'misa')->first()));
    var_dump("yay");
});

Artisan::command('setup', function(){
   Artisan::call('import-countries-images');
   Artisan::call('import-characters-images');
   Artisan::call('import-100-events-all-games');
});
