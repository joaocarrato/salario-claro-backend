<?php

declare(strict_types=1);

use App\Exceptions\InvalidTaxRuleException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        using: function (): void {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));
        },
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            static fn (Request $request, Throwable $exception): bool => $request->is('api/*') || $request->expectsJson()
        );

        $exceptions->render(static function (InvalidTaxRuleException $exception, Request $request) {
            if (! $request->is('api/*')) {
                return null;
            }

            return response()->json([
                'message' => 'Tax rule not found or invalid.',
                'error' => 'INVALID_TAX_RULE',
            ], 422);
        });
    })
    ->create();
