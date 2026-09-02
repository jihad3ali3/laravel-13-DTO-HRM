<?php

namespace App\Providers;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
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
        Gate::before(function ($user, string $ability) {
            return $user->hasRole('admin') ? true : null;
        });

        View::composer('admin.partials.navbar', function ($view) {
            $user = Auth::user();
            $employee = $user?->employee;
            $employeeId = $employee?->id;

            $notifications = collect();
            $unreadCount = 0;

            if ($user) {
                if ($employeeId) {
                    $notifications = Notification::where('employee_id', $employeeId)
                        ->latest('id')
                        ->take(8)
                        ->get();
                    $unreadCount = Notification::where('employee_id', $employeeId)
                        ->whereNull('read_at')
                        ->where('status', '!=', 'read')
                        ->count();
                } else {
                    $notifications = Notification::latest('id')->take(8)->get();
                    $unreadCount = Notification::whereNull('read_at')->where('status', '!=', 'read')->count();
                }
            }

            $view->with([
                'topbarNotifications' => $notifications,
                'topbarUnreadCount' => $unreadCount,
                'currentUser' => $user,
                'currentEmployee' => $employee,
            ]);
        });
    }
}
