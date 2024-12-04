<?php

namespace App\Providers;

use App\Contracts\HtmlParserInterface;
use App\Utilities\HtmlParser;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->registerAppConfigs();
        $this->registerServices();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        JsonResource::withoutWrapping();
    }

    private function registerAppConfigs(): void
    {
        // Set max length for mysql db
        Schema::defaultStringLength(191);

        // For https scheme if not on local machine
        if(!$this->app->environment('local')){
            URL::forceScheme('https');
        }
    }

    private function registerServices(): void
    {
        /**
         * ==================================================
         * Services Interface bindings
         * =================================================
         */
        $this->app->bind(HtmlParserInterface::class, HtmlParser::class);
    }
}
