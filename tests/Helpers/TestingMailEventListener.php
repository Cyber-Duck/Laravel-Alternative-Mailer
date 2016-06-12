<?php
namespace Cyberduck\Test\Mail\Helpers;

use Swift_Events_EventListener;

class TestingMailEventListener implements Swift_Events_EventListener
{
    protected $test;
    public function __construct($test)
    {
        $this->test = $test;
    }
    public function beforeSendPerformed($event)
    {
        $this->test->addEmail($event->getMessage());
        $event->cancelBubble();
    }
}
