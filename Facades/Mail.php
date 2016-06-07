<?php
namespace Cyberduck\Mail\Facades;

use Illuminate\Support\Facades\Facade;

class Mail extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'Cyberduck\Mailer';
    }
}
