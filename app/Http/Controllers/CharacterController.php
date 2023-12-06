<?php

namespace App\Http\Controllers;


use App\Enums\GameEnum;
use App\Http\Resources\Character\CharacterResource;
use App\Models\Character;
use App\Models\Game;
use Error;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CharacterController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            if ($request->input('games')){
                $games_ids = $request->input('games');
                $games = null;
                switch ($games_ids){
                    case "default":
                        $games = Game::orderByRaw("FIELD(id," . implode(",", GameEnum::GAMES_ORDER). ")")->get();
                        break;
                    default:
                        $games_ids_array = explode(',', $games_ids);
                        $games = Game::whereIn('id', $games_ids_array)->orderByRaw("FIELD(id, {$games_ids})")->get();
                }
                $characters_array = [];
                foreach ($games as $game){
                    $characters = Character::where('game_id', $game->id)->orderBy('name', 'ASC')->get();

                    $characters_array[] = ['game' => $game->name, 'characters'=> CharacterResource::collection($characters)];;
                }
                return $this->sendResponse($characters_array, 'Characters retrieved successfully');
            }

            // TODO Correct this, it's not smart to do this because it's the same as the query with the default games parameter
            $games = Game::orderByRaw("FIELD(id," . implode(",", GameEnum::GAMES_ORDER). ")")->get();
            $characters_array = [];
            foreach ($games as $game){
                $characters = Character::where('game_id', $game->id)->orderBy('name', 'ASC')->get();

                $characters_array[] = ['game' => $game->name, 'characters'=> CharacterResource::collection($characters)];;
            }
            return $this->sendResponse($characters_array, 'Characters retrieved successfully');
        } catch (Error $error){
            return $this->sendError($error, ['An error occurred while retrieving the characters E521'], 500);
        }
    }
}
