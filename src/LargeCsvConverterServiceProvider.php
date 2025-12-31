<?php

namespace Noki\LargeCsvConverter;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;

class LargeCsvConverterServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register()
    {
        $this->app->singleton('largecsvconverter', function () {
            return new LargeCsvConverter();
        });
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if (Storage::exists(__DIR__ . '/../../vendor/autoload.php'))
        {
            include __DIR__ . '/../../vendor/autoload.php';
        }

    }
}
