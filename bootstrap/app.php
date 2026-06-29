<?php

use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\SetCustomerBranchContext;
use App\Http\Middleware\SetLocale;
use App\Http\Middleware\SetStaffBranchContext;
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
        $middleware->web(append: [
            SetLocale::class,
        ]);

        $middleware->alias([
            'role' => RoleMiddleware::class,
            'branch.customer' => SetCustomerBranchContext::class,
            'branch.staff' => SetStaffBranchContext::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

        // Debug: Bypass view rendering to capture the exact root exception
        $exceptions->render(function (Throwable $e) {
            if (app()->runningUnitTests()) {
                return;
            }
            header('HTTP/1.1 500 Internal Server Error');
            header('Content-Type: text/plain; charset=utf-8');
            echo "ROOT EXCEPTION CAUGHT BY BOOTSTRAP:\n";
            echo get_class($e).': '.$e->getMessage()."\n";
            echo 'File: '.$e->getFile().' on line '.$e->getLine()."\n\n";
            echo "Stack Trace:\n".$e->getTraceAsString()."\n";
            if ($prev = $e->getPrevious()) {
                echo "\n=========================================\n";
                echo "PREVIOUS EXCEPTION:\n";
                echo get_class($prev).': '.$prev->getMessage()."\n";
                echo 'File: '.$prev->getFile().' on line '.$prev->getLine()."\n\n";
                echo "Stack Trace:\n".$prev->getTraceAsString()."\n";
            }
            exit;
        });
    })->create();
