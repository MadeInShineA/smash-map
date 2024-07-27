<?php

use App\Enums\GameEnum;
use App\Enums\ImageTypeEnum;
use App\Enums\NotificationTypeEnum;
use App\Events\NotificationEvent;
use App\Models\Address;
use App\Models\Character;
use App\Models\Country;
use App\Models\Event;
use App\Models\Image;
use App\Models\Notification;
use App\Models\Scopes\ShownScope;
use App\Models\User;
use App\Notifications\PushNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Pusher\PushNotifications\PushNotifications;


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
        $event_count = $address->events()->withoutGlobalScope(ShownScope::class)->count();
        $user_count = $address->users->count();

        if ($event_count === 0 && $user_count === 0){
            Log::info('Address: ' . $address->name . ' deleted');
//            var_dump('Address: ' . $address->name . ' deleted');
            $address->delete();
        }
    }
});

// TODO Test correctly
Artisan::command('delete-events', function(){
    $events = Event::all();
    $current_time = Carbon::now('UTC');
    foreach ($events as $event){
        $end_date_time = Carbon::parse($event->end_date_time, 'UTC');
        if ($end_date_time < $current_time){
            $image = $event->image;
            $events_with_same_image = Event::where('start_gg_id', $event->start_gg_id)->get();
            $image_has_notification = false;

            foreach ($events_with_same_image as $event_with_same_image){
                if($event_with_same_image->notifications()->exists()){
                    $image_has_notification = true;
                    break;
                }
            }

            if($image && !$image_has_notification){
                $image_directory_path = base_path(). '/storage/app/public/events-images/' . $event->id;
                File::deleteDirectory($image_directory_path);
                $image->delete();
                Log::info('Image for: ' . $event->name . ' deleted');
//                var_dump('Image for: ' . $event->name . ' deleted');

            }
            $event->delete();
            Log::info('Event: ' . $event->name . ' deleted');
//            var_dump('Event: ' . $event->name . ' deleted');
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
//            var_dump('Unknown game');
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
    $events = $response?->data?->tournaments?->nodes;

    if(!$events){
        Log::info('No events found');
        Log::info('Response : ' . json_encode($response));
//        var_dump('No events found');
    }else{
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

                $event_model_instance = Event::withoutGlobalScope(ShownScope::class)->where('start_gg_id', $start_gg_id)->where('game_id', $game_id)->first();
                $event_old_attendees = null;
                if($event_model_instance && $event_model_instance->show){
                    foreach($event_model_instance->subscribed_users()->get() as $user){
                        if($user->has_attendees_notifications){
                            $event_attendees_when_subscribed = $user->pivot->original_attendees;
                            $event_old_attendees = $event_model_instance->attendees;
                            $event_attendees = $game_event->numEntrants;

                            $last_notification = $user->notifications()->where('event_id', $event_model_instance->id)->where('type', NotificationTypeEnum::ATTENDEES)->orderBy('created_at', 'desc')->first();
                            $user_attendees_notifications_thresholds = $user->attendees_notifications_thresholds;

                            foreach ($user_attendees_notifications_thresholds as $user_attendees_notifications_threshold){
                                if($event_attendees_when_subscribed >= $user_attendees_notifications_threshold){
                                    continue;
                                }

                                if($last_notification && $last_notification->attendees >= $user_attendees_notifications_threshold){
                                    continue;
                                }

                                $send_notification = false;
                                if(!$last_notification && $event_attendees >= $user_attendees_notifications_threshold){
                                    $send_notification = true;
                                }elseif ($last_notification && $event_attendees > $last_notification->attendees && $event_attendees >= $user_attendees_notifications_threshold){
                                    $send_notification = true;
                                }

                                if($send_notification){
                                    Log::info('User: ' . $user->username . ' notified for event: ' . $event_model_instance->name . ' User old attendees: ' . $event_old_attendees . ' User new attendees: ' . $event_attendees . ' User attendees notifications thresholds: ' . $user_attendees_notifications_threshold);
//                                    var_dump('User: ' . $user->username . ' notified for event: ' . $event_model_instance->name . ' User old attendees: ' . $event_old_attendees . ' User new attendees: ' . $event_attendees . ' User attendees notifications thresholds: ' . $user_attendees_notifications_threshold);
                                    $message = 'The Event: <a href="' . $event_model_instance->link  .'"target="blank">' . $event_model_instance->name . '</a> has reached ' . $event_attendees . ' attendees!';
                                    $image = $event_model_instance->image?->url;

                                    if(!$image){
                                        $image = URL::to('/storage/map-icons/' . $event_model_instance->game->slug . '.png');
                                    }
                                    $notification = Notification::create(['event_id' => $event_model_instance->id, 'game_id' => $event_model_instance->game_id, 'user_id' => $user->id, 'message' => $message, 'type' => NotificationTypeEnum::ATTENDEES, 'image_url' => $image, 'attendees' => $event_attendees]);
//                                    broadcast(new NotificationEvent($notification, $user));
                                    $user->notify(new PushNotification($notification));
                                    break;
                                }
                            }
                        }

                        if ($user->has_time_notifications){
                            $event_date = Carbon::parse($event_model_instance->start_date_time);
                            $days_until_event = $event_date->diffInDays(Carbon::now());

                            $event_subscription_date = Carbon::parse($user->pivot->created_at);
                            $days_until_event_when_subscribed = $event_date->diffInDays($event_subscription_date);

                            $user_time_notifications_thresholds = $user->time_notifications_thresholds;

                            $last_notification = $user->notifications()->where('event_id', $event_model_instance->id)->where('type', NotificationTypeEnum::TIME)->orderBy('created_at', 'desc')->first();
                            foreach ($user_time_notifications_thresholds as $user_time_notifications_threshold){
                                $send_notification = false;

                                if($days_until_event_when_subscribed <= $user_time_notifications_threshold){
                                    continue;
                                }

                                if($last_notification && $last_notification->days_until_event <= $user_time_notifications_threshold){
                                    continue;
                                }

                                if(!$last_notification && $days_until_event <= $user_time_notifications_threshold){
                                    $send_notification = true;
                                }elseif ($last_notification){
                                    $days_until_event_when_notified = $event_date->diffInDays(Carbon::parse($last_notification->created_at));
                                    if($days_until_event < $days_until_event_when_notified && $days_until_event <= $user_time_notifications_threshold){
                                        $send_notification = true;
                                    }
                                }

                                if($send_notification){
                                    Log::info('User: ' . $user->username . ' notified for event: ' . $event_model_instance->name . ' User days until event: ' . $days_until_event . ' User time notifications thresholds: ' . $user_time_notifications_threshold);
//                                    var_dump('User: ' . $user->username . ' notified for event: ' . $event_model_instance->name . ' User days until event: ' . $days_until_event . ' User time notifications thresholds: ' . $user_time_notifications_threshold);

                                    $message = null;
                                    if($days_until_event === 0) {
                                        $message = 'The Event: <a href="' . $event_model_instance->link . '"target="blank">' . $event_model_instance->name . '</a> starts today!';
                                    }elseif ($days_until_event === 1){
                                        $message = 'The Event: <a href="' . $event_model_instance->link  .'"target="blank">' . $event_model_instance->name . '</a> starts in ' . $days_until_event . ' day!';
                                    }else{
                                        $message = 'The Event: <a href="' . $event_model_instance->link  .'"target="blank">' . $event_model_instance->name . '</a> starts in ' . $days_until_event . ' days!';
                                    }

                                    $image = $event_model_instance->image?->url;

                                    if(!$image){
                                        $image = URL::to('/storage/map-icons/' . $event_model_instance->game->slug . '.png');
                                    }

                                    $notification = Notification::create(['event_id' => $event_model_instance->id, 'game_id' => $event_model_instance->game_id, 'user_id' => $user->id, 'message' => $message, 'type' => NotificationTypeEnum::TIME, 'image_url' => $image]);
//                                    broadcast(new NotificationEvent($notification, $user));
                                    $user->notify(new PushNotification($notification));
                                    break;
                                }
                            }
                        }
                    }
                }
                if(!$event_model_instance || ($event_model_instance->show && ($event_model_instance->start_gg_updated_at < $start_gg_updated_at || $event_model_instance->attendees < $game_event->numEntrants))){
                    $is_online = $game_event->isOnline;
                    $name = $event->name;
                    $timezone = $event->timezone;
                    if ($timezone){
                        $timezone = Carbon::now(new DateTimeZone($timezone))->format('P');
                    }
                    if(!$timezone){
                        $timezone = '+00:00';
                    }

                    $start_date = new DateTime();
                    $start_date->format('Y-m-d H:i:s');

                    $start_date->setTimestamp($game_event->startAt);

                    $end_date = new DateTime();
                    $end_date->format('Y-m-d H:i:s');
                    $end_date->setTimestamp($event->endAt);

                    if ($start_date > $end_date || $end_date < Carbon::now('UTC')){
                        continue;
                    }

                    $attendees = $game_event->numEntrants;

                    $link = 'https://www.start.gg' . $event->url;


                    if($event_model_instance){
                        $event_model_instance->update(['start_gg_updated_at' => $start_gg_updated_at, 'is_online' => $is_online, 'name' => $name, 'timezone' => $timezone, 'start_date_time' => $start_date, 'end_date_time' => $end_date, 'attendees' => $attendees, 'link' => $link]);
                        Log::info('Event: ' . $event->name . ' updated');
//                        var_dump('Event: ' . $event->name . ' updated');
                    }else{
                        $event_model_instance = Event::create(['start_gg_id' => $start_gg_id, 'game_id'=> $game_id, 'start_gg_updated_at' => $start_gg_updated_at, 'is_online' => $is_online, 'name' => $name, 'timezone' => $timezone, 'start_date_time' => $start_date, 'end_date_time' => $end_date, 'attendees' => $attendees, 'link' => $link]);
                        Log::info('Event: ' . $event->name . ' created');
//                        var_dump('Event: ' . $event->name . ' created');
                    }
                    if(!$event_model_instance->is_online){
                        $latitude = $event->lat;
                        $latitude = round($latitude, 7);

                        $longitude = $event->lng;
                        $longitude = round($longitude, 7);

                        $address_name = $event->venueAddress;
                        $country_code = $event->countryCode;

                        $country = Country::where('code',$country_code)->first();

                        # Handle the Oceania case
                        if(!$country){
                            Log::error('Country not found : ' . $country_code . ' for event: ' . $event->name .' with start gg id: ' . $start_gg_id);
//                            var_dump('Country not found : ' . $country_code . ' for event: ' . $event->name .' with start gg id: ' . $start_gg_id);
                            die();
                        }

                        $address = $event_model_instance->address;

                        if(!$address){

                            //FIXME Doesn't work if we add the latitude and longitude => php float vs SQL float ?
                            $address = Address::firstOrCreate(['latitude' => $latitude, 'longitude' => $longitude],['name'=> $address_name, 'country_id' => $country?->id]);
                            $event_model_instance->address_id = $address->id;
                            $event_model_instance->save();
                        }
                    }

                    $event_directory_path = '/events-images/' . $event_model_instance->id;

//                $event_db_md5s = $event_model_instance->images->pluck('md5')->toArray();

                    #TODO Delete the unused images (inside event_db_md5s but not in event_md5s)

//            $event_md5s = [];


                    $images = $event->images;

                    $image = null;
                    foreach ($images as $event_image) {
                        if($event_image->type === 'profile'){
                            $image = $event_image;
                            break;
                        }elseif($event_image->type === 'banner'){
                            $image = $event_image;
                        }
                    }

                    if($image){
                        $image_type = $image->type == 'profile'? ImageTypeEnum::EVENT_PROFILE : ImageTypeEnum::EVENT_BANNER;

                        $image_file = null;
                        try {
                            $image_file = file_get_contents($image->url);
                        }catch (Exception $e){
                            Log::error("Event" . $event->name . " : Image ". $image->url . " couldn't be retrieved");
                        }
                        if($image_file){
                            $image_md5 = md5($image_file);
                            $event_model_instance_old_image = $event_model_instance->image;
                            if($event_model_instance_old_image && $event_model_instance_old_image->md5 === $image_md5){
                                continue;
                            }
                            $isImageStored = Storage::put($event_directory_path . '/' . $image_type . '.png', $image_file);
                            if ($isImageStored){
                                Image::Create(['parentable_type' => Event::class, 'parentable_id' => $event_model_instance->id, 'type' => $image_type,'md5' => $image_md5, 'extension' => 'png', 'origin' => $image->url]);
                                Log::info($image_type .' image for:' . $event->name . ' created');
//                                var_dump($image_type .' image for:' . $event->name . ' created');
                            }else{
                                Log::error("Image ". $image->url . " couldn't be stored stored");
//                                var_dump("Image ". $image->url . " couldn't be stored stored");
                                die();
                            }
                        }
                    }

                    if($event_model_instance->wasRecentlyCreated && !$event_model_instance->is_online){
                        foreach (User::where('has_distance_notifications', true)->get() as $user){
                            if($user->games->contains($event_model_instance->game_id)){
                                $distance_notifications_radius = $user->distance_notifications_radius;
                                $distance = $address->distanceToKM($user->address);

                                if($distance <= $distance_notifications_radius){
                                    Log::info('User: ' . $user->username . ' notified for event: ' . $event_model_instance->name . ' User to event distance: ' . round($distance) . ' User distance notifications radius: ' . $distance_notifications_radius);
//                                    var_dump('User: ' . $user->username . ' notified for event: ' . $event_model_instance->name . ' User to event distance: ' . round($distance) . ' User distance notifications radius: ' . $distance_notifications_radius);
                                    $message = 'The Event: <a href="' . $event_model_instance->link  .'"target="blank">' . $event_model_instance->name . '</a> is happening near you, it\'s only ' . round($distance) . ' kilometers away!';

                                    // Load the image relationship, it's not available it the event was recently created
                                    $event_model_instance->load('image');
                                    $image = $event_model_instance->image?->url;
                                    if(!$image){
                                        $image = URL::to('/storage/map-icons/' . $event_model_instance->game->slug . '.png');
                                    }
                                    $notification = Notification::create(['event_id' => $event_model_instance->id, 'game_id' => $event_model_instance->game_id, 'user_id' => $user->id, 'message' => $message, 'type' => NotificationTypeEnum::DISTANCE, 'image_url' => $image]);
                                    $user->notify(new PushNotification($notification));
//                                    broadcast(new NotificationEvent($notification, $user));
                                }
                            }
                        }
                    }
                }
            }
        }
    }
});

Artisan::command('import-100-events-all-games', function (){
//    var_dump('Starting the 64 import');
    Artisan::call('import-100-events', ['game' => '64']);
//    var_dump('Starting the melee import');
    Artisan::call('import-100-events', ['game' => 'melee']);
//    var_dump('Starting the melee brawl');
    Artisan::call('import-100-events', ['game' => 'brawl']);
//    var_dump('Starting the project + import');
    Artisan::call('import-100-events', ['game' => 'project_+']);
//    var_dump('Starting the project m import');
    Artisan::call('import-100-events', ['game' => 'project_m']);
//    var_dump('Starting the smash4 import');
    Artisan::call('import-100-events', ['game' => 'smash4']);
//    var_dump('Starting the ultimate import');
    Artisan::call('import-100-events', ['game' => 'ultimate']);
});

Artisan::command('import-500-events {game}', function (string $game){
    foreach (range(1, 5) as $page){
            Artisan::call('import-100-events', ['game' => $game, 'page' => $page]);
    }
});

Artisan::command('import-500-events-all-games', function (){
    Log::info('Starting the 64 import');
//    var_dump('Starting the 64 import');
    Artisan::call('import-500-events', ['game' => '64']);
    Log::info('Starting the melee import');
//    var_dump('Starting the melee import');
    Artisan::call('import-500-events', ['game' => 'melee']);
    Log::info('Starting the brawl import');
//    var_dump('Starting the brawl import');
    Artisan::call('import-500-events', ['game' => 'brawl']);
    Log::info('Starting the project + import');
//    var_dump('Starting the project + import');
    Artisan::call('import-500-events', ['game' => 'project_+']);
    Log::info('Starting the project m import');
//    var_dump('Starting the project m import');
    Artisan::call('import-500-events', ['game' => 'project_m']);
    Log::info('Starting the smash4 import');
//    var_dump('Starting the smash4 import');
    Artisan::call('import-500-events', ['game' => 'smash4']);
    Log::info('Starting the ultimate import');
//    var_dump('Starting the ultimate import');
    Artisan::call('import-500-events', ['game' => 'ultimate']);
});

Artisan::command('import-characters-images',function(){
    $characters = Character::all();

    foreach ($characters as $character){
        Image::Create(['parentable_type' =>Character::class, 'parentable_id' =>$character->id, 'type' => ImageTypeEnum::ICON, 'extension' => 'png']);
//        var_dump('Image for: ' . $character->name . ' created');
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
        Image::Create(['parentable_type' =>Country::class, 'parentable_id' =>$country->id, 'type' => ImageTypeEnum::ICON, 'extension' => 'png']);
//        var_dump('Image for: ' . $country->name . ' created');
    }

});

Artisan::command('setup', function(){
   Artisan::call('import-countries-images');
   Artisan::call('import-characters-images');
//   Artisan::call('import-100-events-all-games');
});

Artisan::command('test-push-notification {username}', function(string $username){
    $notification = Notification::first();
    $user = User::where("username", $username)->first();

    $user->notify(new PushNotification($notification));
});

Artisan::command('test-ws-notification {username}', function(string $username){
    $notification = Notification::first();
    $user = User::where("username", $username)->first();
    broadcast(new NotificationEvent($notification, $user));
});

Artisan::command('move-events-images', function(){
    $events = Event::all();
    foreach ($events as $event){

        $event_file_slug = Str::slug($event->name)?? Str::slug('id-' . $event->id);
        $old_event_directory_path = '/events-images/' . $event_file_slug;
        $new_event_directory_path = '/events-images/' . $event->id;
        $image = $event->image;

        if($image){
            $image_old_path = $old_event_directory_path . '/' . $image->type . '.' . $image->extension;

            if(file_exists(base_path(). '/storage/app/public' . $image_old_path)){
                Storage::move($old_event_directory_path, $new_event_directory_path);
            }
        }
    }
});

Artisan::command('reload-events-images', function(){
    $events = Event::all();
    foreach ($events as $event){
        $start_gg_id = $event->start_gg_id;
        $apiToken = env('START_GG_API_KEY');
        $endpointUrl = 'https://api.start.gg/gql/alpha';

        $query = 'query TournamentQuery($id: ID) {
            tournament(id: $id) {
                images {
                url
                type
                }
            }
        }';

        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiToken,
        ];

        $data = [
            'query' => $query,
            'variables' => ['id' => $start_gg_id],
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
        $images = $response->data?->tournament?->images;

        if($images){
            $image = null;
            foreach ($images as $event_image) {
                if ($event_image->type === "profile") {
                    $image = $event_image;
                    break;
                } elseif ($event_image->type === "banner") {
                    $image = $event_image;
                }
            }

            if ($image){

                $event_directory_path = '/events-images/' . $event->id;

                $image_type = $image->type == 'profile'? ImageTypeEnum::EVENT_PROFILE : ImageTypeEnum::EVENT_BANNER;

                $image_file = file_get_contents($image->url);

                Storage::put($event_directory_path . '/' . $image_type . '.png', $image_file);

                $image_md5 = md5($image_file);

                Image::updateOrCreate(['parentable_type' => Event::class, 'parentable_id' => $event->id, 'type' => $image_type, 'extension' => 'png', 'md5' => $image_md5], ['origin' => $image->url]);



            }
        }
    }
});



