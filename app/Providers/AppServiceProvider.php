<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Notifikasi;
use Illuminate\Support\Facades\View;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;


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
        setlocale(LC_TIME, 'id_ID.UTF-8');
        \Carbon\Carbon::setLocale('id');
        View::composer('includes.superadmin.navbar', function ($view) {
            if (Auth::check()) {
                $userDivisi = Auth::user()->divisi_id_divisi;
                $notifications = Notifikasi::where('id_divisi', $userDivisi)
                    ->orderBy('updated_at', 'desc') 
                    ->limit(5)
                    ->get();
            } else {
                $notifications = collect([]);
            }
            $view->with('notifications', $notifications);
        });
        
    }

}
