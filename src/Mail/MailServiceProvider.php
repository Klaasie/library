<?php namespace October\Rain\Mail;

use Illuminate\Mail\MailServiceProvider as MailServiceProviderBase;

class MailServiceProvider extends MailServiceProviderBase
{
    /**
     * Register the Illuminate mailer instance. Carbon copy of Illuminate method.
     * @return void
     */
    protected function registerIlluminateMailer()
    {
        $this->app->singleton('mail.manager', function ($app) {
            return new MailManager($app);
        });

        $this->app->bind('mailer', function ($app) {
            return $app->make('mail.manager')->mailer();
        });
    }
}
