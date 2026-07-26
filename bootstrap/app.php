<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Render sits behind its own reverse proxy that terminates HTTPS
        // before traffic reaches this container - without trusting it,
        // url()/asset() would generate http:// links instead of https://.
        //
        // Conditional on Render's own RENDER env var (which Render sets
        // automatically on every service - see
        // https://render.com/docs/environment-variables) rather than
        // applied unconditionally: that keeps Laravel's normal, safer
        // default behavior fully intact for anyone running this locally
        // or deploying it to their own hosting instead, with nothing to
        // manually remove.
        if (env('RENDER')) {
            $middleware->trustProxies(at: '*');
        }
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();