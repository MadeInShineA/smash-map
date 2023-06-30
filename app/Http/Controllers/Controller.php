<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;


class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

//    public function getVisitorTimezone(Request $request)
//    {
////        $ip = $request->ip();
//        $ip = '89.152.104.3';
//        $url = 'http://ip-api.com/json/'.$ip;
//        $timeZone = file_get_contents($url);
//        $timeZone = json_decode($timeZone,true)['timezone'];
//        return($timeZone);
//    }

    public function sendResponse($data, $message, int $code = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'data'    => $data,
            'message' => $message,
            'statusCode' => $code,
        ];

        return response()->json($response, $code);
    }

    public function sendError($message, array $errorMessages = [], int $code = 404): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
            'statusCode' => $code,
        ];

        if(! empty($errorMessages)){
            $response['errors'] = $errorMessages;
        }

        return response()->json($response, $code);
    }

}
