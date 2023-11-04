<?php

namespace App\Rules;

use App\Models\Character;
use App\Models\Game;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class AtLeastOneCharacterPerGame implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $games_ids = request()->input('games');

        $characters_id = request()->input('characters');
        $characters_game_ids = Character::whereIn('id', $characters_id)->pluck('game_id')->toArray();

        if(!empty(array_diff($games_ids, $characters_game_ids)))
            $fail('At least one character per game is required');

    }
}
