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
        // 仅从 Authorization 头读取，不接受 URL query / body 传 token（避免凭据经日志/历史/Referer 泄露）
        $token = $request->header('Authorization');
        
        if (!$token) {
            return response()->json(['message' => 'Unauthorized. No API Token provided.'], 401);
        }

        if (str_starts_with($token, 'Bearer ')) {
            $token = substr($token, 7);
        }

        $hashed = hash('sha256', $token);
        $user = User::where('api_token', $hashed)->first();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized. Invalid API Token.'], 401);
        }

        auth()->login($user);

        return $next($request);
    }
}