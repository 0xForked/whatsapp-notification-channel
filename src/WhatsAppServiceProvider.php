<?php

namespace NotificationChannels\WhatsApp;

use Blade;
use GuzzleHttp\Client as HttpClient;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;

class WhatsAppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->envVar();

        $this->configureComponents();

        $this->app->when(WhatsAppChannel::class)
            ->needs(WhatsApp::class)
            ->give(static function () {
                return new WhatsApp(
                    app(HttpClient::class),
                    config('services.whatsapp-notification-service.service_api_url')
                );
            });
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        foreach (glob(__DIR__.'/Helpers/*.php') as $filename){
            require_once($filename);
        }

        Notification::resolved(function (ChannelManager $service) {
            $service->extend('whatsapp', function ($app) {
                return new WhatsAppChannel(
                    new WhatsApp(
                        app(HttpClient::class),
                        config('services.whatsapp-worker.worker_api_uri')
                    ),
                    app(Dispatcher::class)
                );
            });
        });
    }

    protected function configureComponents()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'whatsapp');

        $this->callAfterResolving(BladeCompiler::class, function () {
            Blade::component('whatsapp::components.account', 'whatsapp-account');
        });
    }

    protected function envVar()
    {
        $path = $this->envPath();

        if (\Str::contains(file_get_contents($path), 'WHATSAPP_SERVICE_URL') === false) {
            // create new entry
            file_put_contents($path,
                PHP_EOL."WHATSAPP_SERVICE_URL=SERVICE_URL_HERE".PHP_EOL,
                FILE_APPEND);
        } else {
            $service_url = config('services.whatsapp-notification-service.service_api_url');
            \Log::info("WhatsApp Notification Service Url: $service_url");
        }
    }

    protected function envPath(): string
    {
        if (method_exists($this->app, 'environmentFilePath')) {
            return $this->app->environmentFilePath();
        }

        return $this->app->basePath('.env');
    }
}
