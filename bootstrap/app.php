<?php

use App\Http\Middleware\EnsureSubscribed;
use App\Http\Middleware\RestrictUntilAdminVerified;
use App\Http\Middleware\SchoolProfileComplete;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\LocaleMiddleware;

$app = Application::configure(basePath: dirname(__DIR__))
  ->withRouting(
    web: __DIR__ . '/../routes/web.php',
    api: __DIR__ . '/../routes/api.php',
    commands: __DIR__ . '/../routes/console.php',
    health: '/up',
  )
  ->withMiddleware(function (Middleware $middleware) {
    $middleware->trustProxies(at: '*');
    $middleware->web(LocaleMiddleware::class);
    $middleware->alias([
        'restrict.admin' => RestrictUntilAdminVerified::class,
        'restrict.school.profile.setup' => SchoolProfileComplete::class,
        'ensure.subscribed' => EnsureSubscribed::class,

    ]);
  })
  ->withExceptions(function (Exceptions $exceptions) {
    //
  })->create();


$app->booted(function () {
    \Illuminate\Support\Facades\Route::middleware('api')
        ->prefix('webhook')
        ->group(base_path('routes/webhook.php'));
});

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

return $app;
