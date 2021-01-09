<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use TCG\Voyager\Facades\Voyager;
use Illuminate\Events\Dispatcher;

class AppServiceProvider extends ServiceProvider
{
    
    public function register()
    {
        //
    }

    
    public function boot()
    {
        Voyager::addAction(\App\Actions\SendSMS::class);
        Voyager::addAction(\App\Actions\AssignAction::class);
        
    }
}
