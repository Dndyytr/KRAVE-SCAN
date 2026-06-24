<?php

namespace App\Providers;

use App\Events\OrderCreated;
use App\Events\OrderPaid;
use App\Listeners\LogSuccessfulLogin;
use App\Listeners\LogSuccessfulLogout;
use App\Listeners\SendOrderCreatedNotifications;
use App\Listeners\TriggerOrderAutomations;
use App\Models\Branch;
use App\Models\Order;
use App\Observers\OrderObserver;
use App\Services\ActivityLogger;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ActivityLogger::class, function ($app) {
            return new ActivityLogger;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Order::observe(OrderObserver::class);
        Event::listen(
            OrderPaid::class,
            TriggerOrderAutomations::class
        );
        Event::listen(
            OrderCreated::class,
            SendOrderCreatedNotifications::class
        );

        Event::listen(
            Login::class,
            LogSuccessfulLogin::class
        );

        Event::listen(
            Logout::class,
            LogSuccessfulLogout::class
        );

        view()->composer('*', function ($view) {
            if (auth()->check() && is_null(auth()->user()->branch_id)) {
                $view->with('globalBranches', Branch::all());
            }
        });
    }
}
