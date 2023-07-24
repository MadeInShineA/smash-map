<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InternalDomain
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

//        $requestHost = $request->getHost();
//
//        if ($requestHost == env('SMASH_MAP_URL')) {
//            return $next($request);
//        }
//        return response()->json(['message' => 'Forbidden - 403E - ' . $requestHost], 403);
    }
}
