<?php

namespace App\Http\Controllers;

use App\Enums\GameEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\Character\CharacterResource;
use App\Models\Character;
use App\Models\Game;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CharacterController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            if ($request->input('game')){
                $game = $request->input('game');
                $characters = Character::query();
                switch ($game){
                    case GameEnum::ULTIMATE:
                    case GameEnum::BRAWL:
                    case GameEnum::SMASH64:
                    case GameEnum::SMASH4:
                    case GameEnum::MELEE:
                        $characters->where('game_id', $game);
                        break;
                    case GameEnum::PROJECT_M:
                        $characters->where('game_id', GameEnum::BRAWL)->orWhere('game_id', $game);
                        break;
                    case GameEnum::PROJECT_PLUS:
                        $characters->where('game_id', GameEnum::BRAWL)->orWhere('game_id', GameEnum::PROJECT_M)->orWhere('game_id', $game);
                        break;
                    default:
                        return $this->sendError('Incorrect game id');
                }
                return $this->sendResponse(CharacterResource::collection($characters->get()), 'Characters retrieved successfully');
            }
            return $this->sendResponse([], 'Characters retrieved successfully');
        } catch (Exception $exception){
            return $this->sendError($exception, ['An error occurred while retrieving the characters'], 500);
        }
    }
}
