<?php

namespace App\Providers;

use App\Models\Setting;
use App\Models\User;
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

            $user = auth('admin')->user(); // vì bạn dùng guard admin

            $notifications = collect();
            $unreadCount = 0;

            if ($user) {
                /**
                 * @var User $user
                 */
                $notifications = $user->notifications()->take(10)->get();
                $unreadCount = $user->unreadNotifications()->count();
            }

            $setting = Setting::query()->firstOrCreate();

            View::share([
                'setting' => $setting,
                'notifications' => $notifications,
                'unreadCount' => $unreadCount
            ]);
        });
    }
}
