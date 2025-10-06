<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Appointment;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
    $this->app->bind(
    \App\Repositories\HearingAidRepositoryInterface::class,
    \App\Repositories\HearingAidRepository::class
    );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share appointment count to all views (simple notification badge)
        View::composer('*', function ($view) {
            try {
                $total = Appointment::count();
                $unseen = Appointment::whereNull('seen_at')->count();
            } catch (\Exception $e) {
                $total = 0;
                $unseen = 0;
            }
            $view->with('appointmentsCount', $total)
                 ->with('appointmentsUnseenCount', $unseen);
        });
    }
}
