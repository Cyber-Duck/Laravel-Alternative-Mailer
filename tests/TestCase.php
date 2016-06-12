<?php
namespace Cyberduck\Test\Mail;

use Cyberduck\Mail\Facades\Mail as Mail2;
use Cyberduck\Test\Mail\Helpers\MailTracking;
use Cyberduck\Test\Mail\Helpers\TestingMailEventListener;

class TestCase extends \Illuminate\Foundation\Testing\TestCase
{
    use MailTracking;

    protected $baseUrl = 'http://localhost';

    protected $testName = "Simone Todaro";
    protected $testEmail = "simone@cyber-duck.co.uk";
    protected $testSubject = "This is a test";

    public function createApplication()
    {
        $app = require __DIR__.'/../vendor/laravel/laravel/bootstrap/app.php';
        $app->register(\Cyberduck\Mail\MailServiceProvider::class);
        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        // Set a valid key
        $app["config"]["app"] = array_merge(
            $app["config"]["app"],
            ["key" =>"SomeRandomStringSomeRandomString"]
        );

        // Include package config file
        $app["config"]["mail2"] = require __DIR__.'/config/config.php';

        // Create a test route
        $app['router']->get('/test', function () {
            Mail2::send('test::email', [], function ($m) {
                $m->to($this->testEmail, $this->testName)
                    ->subject($this->testSubject);
            });
            return view('test::success');
        });

        $app['view']->addNamespace('test', __DIR__.'/views');

        Mail2::getSwiftMailer()
            ->registerPlugin(new TestingMailEventListener($this));

        return $app;
    }

    public function testIntegration()
    {
        $this->assertTrue($this->app->bound('cyberduck.swift.transport'));
        $this->assertTrue($this->app->bound('cyberduck.swift.mailer'));
        $this->assertTrue($this->app->bound('cyberduck.mailer'));
        $this->assertInstanceOf(
            \Cyberduck\Mail\TransportManager::class,
            $this->app->make('cyberduck.swift.transport')
        );
    }

    public function testBehaviour()
    {
        $config = require __DIR__.'/config/config.php';

        $this->visit('/test')
            ->see('Mail sent!')
            ->seeEmailWasSent()
            ->seeEmailFrom($config['from']['address'])
            ->seeEmailFromName($config['from']['name'])
            ->seeEmailTo($this->testEmail)
            ->seeEmailToName($this->testName)
            ->seeEmailSubject($this->testSubject)
            ->seeEmailContains('Hello World!');
    }
}
