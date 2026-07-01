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
use App\Models\Permission;
use App\Observers\OrderObserver;
use App\Services\ActivityLogger;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
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
        // Commented out to prevent double execution since Event Discovery auto-registers it
        // Event::listen(
        //     OrderCreated::class,
        //     SendOrderCreatedNotifications::class
        // );

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

        // Force HTTPS in production (like Vercel)
        if (config('app.env') === 'production') {
            URL::forceScheme('https');

            // Ensure compiled view path exists in writable serverless /tmp
            $compiledPath = config('view.compiled');
            if ($compiledPath && ! is_dir($compiledPath)) {
                @mkdir($compiledPath, 0755, true);
            }
        }

        // Register dynamic gates from database
        Gate::before(function ($user, $ability) {
            if ($user->role && $user->role->name === 'admin') {
                return true;
            }
        });

        if (Schema::hasTable('permissions')) {
            try {
                $permissions = Permission::all();
                foreach ($permissions as $permission) {
                    Gate::define($permission->name, function ($user) use ($permission) {
                        return $user->role && $user->role->permissions->contains('name', $permission->name);
                    });
                }
            } catch (\Throwable $e) {
                // Silence migration or DB connection errors
            }
        }
    }
}
