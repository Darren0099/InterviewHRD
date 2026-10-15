<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Schema::defaultStringLength(191);

        // Wasmer Edge menyuntikkan DB_HOST, DB_PORT, DB_NAME, DB_USERNAME, DB_PASSWORD.
        // Laravel membaca DB_DATABASE, jadi dipetakan di sini.
        if ($name = env('DB_NAME')) {
            config([
                'database.default'                    => 'mysql',
                'database.connections.mysql.database' => $name,
            ]);
        }

        // Pastikan link CSS/JS memakai https saat dipasang di hosting
        if (env('FORCE_HTTPS', false)) {
            URL::forceScheme('https');
        }
    }
}
