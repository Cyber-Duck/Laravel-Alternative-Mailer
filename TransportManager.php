<?php

namespace Cyberduck\Mail;

use Aws\Ses\SesClient;
use Illuminate\Support\Arr;
use Illuminate\Support\Manager;
use GuzzleHttp\Client as HttpClient;
use Swift_SmtpTransport as SmtpTransport;
use Swift_MailTransport as MailTransport;
use Illuminate\Mail\Transport\LogTransport;
use Illuminate\Mail\Transport\MailgunTransport;
use Illuminate\Mail\Transport\MandrillTransport;
use Illuminate\Mail\Transport\SesTransport;
use Swift_SendmailTransport as SendmailTransport;
use Illuminate\Mail\TransportManager as OriginalTransportManager;

class TransportManager extends OriginalTransportManager
{
    /**
     * Create an instance of the SMTP Swift Transport driver.
     *
     * @return \Swift_SmtpTransport
     */
    protected function createSmtpDriver()
    {
        $config = $this->app['config']['mail2'];

        // The Swift SMTP transport instance will allow us to use any SMTP backend
        // for delivering mail such as Sendgrid, Amazon SES, or a custom server
        // a developer has available. We will just pass this configured host.
        $transport = SmtpTransport::newInstance(
            $config['host'],
            $config['port']
        );

        if (isset($config['encryption'])) {
            $transport->setEncryption($config['encryption']);
        }

        // Once we have the transport we will check for the presence of a username
        // and password. If we have it we will set the credentials on the Swift
        // transporter instance so that we'll properly authenticate delivery.
        if (isset($config['username'])) {
            $transport->setUsername($config['username']);
            $transport->setPassword($config['password']);
        }

        return $transport;
    }

    /**
     * Create an instance of the Sendmail Swift Transport driver.
     *
     * @return \Swift_SendmailTransport
     */
    protected function createSendmailDriver()
    {
        $command = $this->app['config']['mail2']['sendmail'];
        return SendmailTransport::newInstance($command);
    }

    /**
     * Get the default cache driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->app['config']['mail2.driver'];
    }

    /**
     * Set the default cache driver name.
     *
     * @param  string  $name
     * @return void
     */
    public function setDefaultDriver($name)
    {
        $this->app['config']['mail2.driver'] = $name;
    }
}
