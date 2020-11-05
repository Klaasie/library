<?php

declare(strict_types=1);

namespace October\Rain\Mail;

use InvalidArgumentException;

class MailManager extends \Illuminate\Mail\MailManager
{
    protected function resolve($name)
    {
        /*
         * Extensibility
         */
        $this->app['events']->fire('mailer.beforeRegister', [$this]);

        $config = $this->getConfig($name);

        if (is_null($config)) {
            throw new InvalidArgumentException("Mailer [{$name}] is not defined.");
        }

        // Once we have created the mailer instance we will set a container instance
        // on the mailer. This allows us to resolve mailer classes via containers
        // for maximum testability on said classes instead of passing Closures.
        $mailer = new Mailer(
            $name,
            $this->app['view'],
            $this->createSwiftMailer($config),
            $this->app['events']
        );

        if ($this->app->bound('queue')) {
            $mailer->setQueue($this->app['queue']);
        }

        // Next we will set all of the global addresses on this mailer, which allows
        // for easy unification of all "from" addresses as well as easy debugging
        // of sent messages since these will be sent to a single email address.
        foreach (['from', 'reply_to', 'to', 'return_path'] as $type) {
            $this->setGlobalAddress($mailer, $config, $type);
        }

        /*
         * Extensibility
         */
        $this->app['events']->fire('mailer.register', [$this, $mailer]);

        return $mailer;
    }
}
