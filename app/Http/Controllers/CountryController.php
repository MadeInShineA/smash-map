<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\Country\CountryFilterResource;
use App\Http\Resources\Country\CountryResource;
use App\Models\Country;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use PHPUnit\Framework\Constraint\Count;

class CountryController extends Controller
{
    public function countries_filter (Request $request):JsonResponse
    {
        $continent = $request->input('continent');
        $countries = Country::query();
        switch ($continent){
            case 'default':
                break;
            default:
                $countries->continent($continent);
        }
        return $this->sendResponse(CountryFilterResource::collection($countries->get()), 'Countries retrieved with success');
    }
}
