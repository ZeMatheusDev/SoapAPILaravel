<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
use Illuminate\Http\Request;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Config;

class JwtMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['error' => 'Token não fornecido'], 401);
        }

        try {
            $decoded = JWT::decode($token, new Key(Config::get('jwt.secret'), 'HS256'));
            $userId = $decoded->sub; 
            $request->attributes->add(['user_id' => $userId]);
        } catch (ExpiredException $e) {
            return response()->json(['error' => 'Token expirado'], 401);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Token inválido'], 401);
        }

        return $next($request);
    }
}