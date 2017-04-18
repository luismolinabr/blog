<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // date_default_timezone_set('America/Sao_Paulo');

        Carbon::setLocale(config('app.locale'));

        setlocale(LC_TIME, 'pt-BR');

        view()->composer('layouts.sidebar', function ($view) {
            $view->with('archives', \App\Post::archives());
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }
}
