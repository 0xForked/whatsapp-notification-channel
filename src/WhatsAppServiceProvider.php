<?php

namespace NotificationChannels\WhatsApp;

use GuzzleHttp\Client as HttpClient;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ServiceProvider;

class WhatsAppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->app->when(WhatsAppChannel::class)
            ->needs(WhatsApp::class)
            ->give(static function () {
                return new WhatsApp(
                    app(HttpClient::class),
                    config('services.whatsapp-worker.worker_api_uri')
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
}
