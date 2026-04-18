<?php

namespace App\Providers;

use App\Models\Booking;
use App\Models\Instrument;
use App\Models\Queue;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

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
        Paginator::defaultView('vendor.pagination.tailwind');
        Paginator::defaultSimpleView('vendor.pagination.tailwind');

        View::composer('layouts.main', function ($view) {
            $session = request()->session();
            $userEmail = (string) $session->get('web_user_email', '');

            $categories = Instrument::query()
                ->whereNotNull('category')
                ->where('category', '!=', '')
                ->distinct()
                ->orderBy('category')
                ->pluck('category');

            $locations = Instrument::query()
                ->whereNotNull('location')
                ->where('location', '!=', '')
                ->distinct()
                ->orderBy('location')
                ->pluck('location');

            $notificationCount = 0;
            if ($userEmail !== '') {
                $notificationCount = Booking::query()
                    ->where(function ($q) use ($userEmail) {
                        $q->where('email', $userEmail)
                            ->orWhere('user_email', $userEmail);
                    })
                    ->whereIn('status', ['approved', 'rejected'])
                    ->where('updated_at', '>=', now()->subDays(10))
                    ->count();

                $notificationCount += Queue::query()
                    ->where('email', $userEmail)
                    ->whereIn('status', ['approved', 'rejected'])
                    ->where('updated_at', '>=', now()->subDays(10))
                    ->count();
            }

            $view->with([
                'navCategories' => $categories,
                'navLocations' => $locations,
                'navBagCount' => count($session->get('web_bag', [])),
                'navFavoriteCount' => count($session->get('web_favorites', [])),
                'navNotificationCount' => $notificationCount,
            ]);
        });
    }
}
