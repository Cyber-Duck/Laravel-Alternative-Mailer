<?php

namespace Cyberduck\Mail;

use Swift_Mailer;
use Illuminate\Support\ServiceProvider;
use Illuminate\Mail\Mailer;

class MailServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Set the service provider up for publishing the configuration file
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/config.php' => config_path('mail2.php'),
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerSwiftMailer();

        $this->app->singleton('cyberduck.mailer', function ($app) {
            // Once we have create the mailer instance, we will set a container instance
            // on the mailer. This instance will use the secondary transport manager.
            $mailer = new Mailer(
                $app['view'],
                $app['cyberduck.swift.mailer'],
                $app['events']
            );

            $this->setMailerDependencies($mailer, $app);

            // If a "from" address is set, we will set it on the mailer so that all mail
            // messages sent by the applications will utilize the same "from" address
            // on each one, which makes the developer's life a lot more convenient.
            $from = $app['config']['mail2.from'];
            if (is_array($from) && isset($from['address'])) {
                $mailer->alwaysFrom($from['address'], $from['name']);
            }

            $to = $app['config']['mail2.to'];
            if (is_array($to) && isset($to['address'])) {
                $mailer->alwaysTo($to['address'], $to['name']);
            }

            return $mailer;
        });
    }

    /**
     * Set a few dependencies on the mailer instance.
     *
     * @param  \Illuminate\Mail\Mailer  $mailer
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function setMailerDependencies($mailer, $app)
    {
        $mailer->setContainer($app);

        if ($app->bound('Psr\Log\LoggerInterface')) {
            $mailer->setLogger($app->make('Psr\Log\LoggerInterface'));
        }

        if ($app->bound('queue')) {
            $mailer->setQueue($app['queue.connection']);
        }
    }

    /**
     * Register the Swift Mailer instance.
     *
     * @return void
     */
    public function registerSwiftMailer()
    {
        $this->registerSwiftTransport();

        // Once we have the transporter registered, we will register the actual Swift
        // mailer instance, passing in the transport instances, which allows us to
        // override this transporter instances during app start-up if necessary.
        $this->app['cyberduck.swift.mailer'] = $this->app->share(function ($app) {
            return new Swift_Mailer($app['cyberduck.swift.transport']->driver());
        });
    }

    /**
     * Register the Swift Transport instance.
     *
     * @return void
     */
    protected function registerSwiftTransport()
    {
        $this->app['cyberduck.swift.transport'] = $this->app->share(function ($app) {
            return new TransportManager($app);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['cyberduck.mailer', 'cyberduck.swift.mailer', 'cyberduck.swift.transport'];
    }
}
