<?php
namespace App\Http\Middleware;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\Patient;

class JwtMiddleware
{
    public function handle($request, Closure $next)
    {
        try {
            $token = $request->bearerToken();
            if (!$token) {
                throw new \Exception('No token provided');
            }

            $decoded = JWT::decode($token, new Key(env('JWT_SECRET'), 'HS256'));
            $patient = Patient::findOrFail($decoded->sub);

            Log::info('JWT OK', ['patient' => $patient]);
            $request->merge(['patient' => $patient]);

            return $next($request);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }
}
