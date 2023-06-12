<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;


class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function getVisitorTimezone(Request $request)
    {
//        $ip = $request->ip();
        $ip = '89.152.104.3';
        $url = 'http://ip-api.com/json/'.$ip;
        $timeZone = file_get_contents($url);
        $timeZone = json_decode($timeZone,true)['timezone'];
        return($timeZone);
    }
}
