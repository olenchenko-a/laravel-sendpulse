<?php
declare(strict_types=1);

namespace LaravelSendpulseMail;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;

class SendpulseMailServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(): void
    {
        Mail::extend('sendpulse', function (array $config = []) {
            return new SendpulseMailTransport($config['api_user_id'], $config['api_secret']);
        });
    }
}
