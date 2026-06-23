<?php

namespace App\Providers;

use App\Services\BranchContext;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class BranchServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(BranchContext::class, function ($app) {
            return new BranchContext;
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Share current branch context with all views dynamically
        View::composer('*', function ($view) {
            $view->with('currentBranch', app(BranchContext::class)->getBranch());
        });
    }
}
