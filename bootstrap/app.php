<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Http\Middleware\JwtMiddleware;
use Tymon\JWTAuth\Http\Middleware\Authenticate;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
         $middleware->alias([
             'auth' => \App\Http\Middleware\Authenticate::class,
         ]);

         // Usuń lub zmień tutaj
         $middleware->alias([
            'jwt.auth' => \App\Http\Middleware\JwtMiddleware::class,
         ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
            $exceptions->render(function (UnauthorizedHttpException $e, Request $request) {
                return response()->json(['error' => 'Unauthorized'], 401);
            });
        
            $exceptions->render(function (NotFoundHttpException $e, Request $request) {
                return response()->json(['error' => 'Not Found'], 404);
            });
    })->create();

// Zarejestruj alias middleware bez kropki:
$app->router->aliasMiddleware('authapi', \Tymon\JWTAuth\Http\Middleware\Authenticate::class);

return $app;