<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'portal/webhooks/payment',
            'portal/webhooks/payment*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Database\QueryException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                // Connection Refused / Timeout code often 2002
                if ($e->getCode() === 2002 || str_contains($e->getMessage(), 'Connection refused') || str_contains($e->getMessage(), 'server has gone away')) {
                    return response()->json([
                        'message' => 'Tidak dapat terhubung ke database/server. Mohon coba beberapa saat lagi.',
                        'error' => 'Connection Error'
                    ], 503);
                }
            }
        });

        $exceptions->render(function (\PDOException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                if ($e->getCode() === 2002 || str_contains($e->getMessage(), 'Connection refused')) {
                    return response()->json([
                        'message' => 'Tidak dapat terhubung ke database. Mohon periksa koneksi anda.',
                        'error' => 'Database Connection Error'
                    ], 503);
                }
            }
        });

        $exceptions->render(function (\RedisException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Tidak dapat terhubung ke server cache (Redis). Mohon coba beberapa saat lagi.',
                    'error' => 'Redis Connection Error'
                ], 503);
            }
        });

        // Handle Predis Connection Exceptions (e.g. Upstash)
        $exceptions->render(function (\Predis\Connection\Resource\Exception\StreamInitException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Tidak dapat terhubung ke server cache (Upstash/Redis). Mohon periksa konfigurasi host.',
                    'error' => 'Redis Connection Error'
                ], 503);
            }
        });
    })->create();
