<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'clinic.tenant' => \App\Http\Middleware\EnsureUserBelongsToClinic::class,
            'plan.limit' => \App\Http\Middleware\CheckPlanLimits::class,
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            '/webhook/whatsapp',
            '/stripe/webhook',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\App\Exceptions\PlanLimitReachedException $e, \Illuminate\Http\Request $request) {
            $message = $e->getMessage();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['error' => $message], 403);
            }

            return redirect()->route('billing.plans')->with('error', $message);
        });
    })->create();
