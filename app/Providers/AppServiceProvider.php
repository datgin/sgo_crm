<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function () {
            static $shared = false;

            if ($shared) return;
            $shared = true;

            $setting = Setting::query()->firstOrCreate();

            View::share(['setting' => $setting]);
        });
    }
}
