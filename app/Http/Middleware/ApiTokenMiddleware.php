<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class ApiTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('Authorization') ?: $request->input('api_token');
        
        if (!$token) {
            return response()->json(['message' => 'Unauthorized. No API Token provided.'], 401);
        }

        if (str_starts_with($token, 'Bearer ')) {
            $token = substr($token, 7);
        }

        $user = User::where('api_token', $token)->first();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized. Invalid API Token.'], 401);
        }

        // Authenticate the user for the current request
        auth()->login($user);

        return $next($request);
    }
}