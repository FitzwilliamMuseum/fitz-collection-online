<?php

namespace App\Providers;

use App\Console\Commands\ApiLogClear;
use Illuminate\Support\ServiceProvider;
use App\Http\Middleware\ApiLogMiddleware;

class ApiLogServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadCommands();
        $this->loadMiddleware();
    }

    public function register()
    {

    }

    public function loadMiddleware()
    {
        app('router')->aliasMiddleware('api-log', ApiLogMiddleware::class);
    }

    public function loadCommands()
    {
        $this->commands(ApiLogClear::class);
    }
}
