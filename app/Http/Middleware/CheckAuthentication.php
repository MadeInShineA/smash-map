<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAuthentication
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

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

    public function handle(Request $request, Closure $next): JsonResponse | Response
    {
        $user = $request->route('user');
        if ($user->id != $request->user('sanctum')->id) {
            return $this->sendError('You are not authorized', [], 401);
        }
        return $next($request);
    }
}
