# Laravel Alternative Mailer
[![Build Status](https://travis-ci.org/Cyber-Duck/Laravel-Alternative-Mailer.svg?branch=master)](https://travis-ci.org/Cyber-Duck/Laravel-Alternative-Mailer)
[![Latest Stable Version](https://poser.pugx.org/cyber-duck/laravel-alternative-mailer/v/stable)](https://packagist.org/packages/cyber-duck/laravel-alternative-mailer)
[![Total Downloads](https://poser.pugx.org/cyber-duck/laravel-alternative-mailer/downloads)](https://packagist.org/packages/cyber-duck/laravel-alternative-mailer)
[![License](https://poser.pugx.org/cyber-duck/laravel-alternative-mailer/license)](https://packagist.org/packages/cyber-duck/laravel-alternative-mailer)

This package allows a Laravel 5 application to send emails through two different mail configurations. This package has been adapted from [illuminate/mail](https://github.com/illuminate/mail) by Taylor Otwell.

Author: [Simone Todaro](https://github.com/SimoTod)

## Install
Require this package with composer:
```
composer require cyber-duck/laravel-alternative-mailer:~1.0.2
```

After updating composer, add the ServiceProvider to the providers array in `config/app.php`
```php
'providers' => array(
    ...
    'Cyberduck\Mail\MailServiceProvider'
)
```

And add an alias in `config/app.php`:
```php
'aliases' => array(
    'Mail2' => 'Cyberduck\Mail\Facades\Mail',
)
```

Copy the package config to your local config with the publish command:
```
php artisan vendor:publish --provider="Cyberduck\Mail\MailServiceProvider"
```

Finally, set up your configuration in `config/mail2.php`

### Usage
To send an email with the alternative configuration, use the Mail2 facade with the same syntax of the [Mail facade](https://laravel.com/docs/master/mail#sending-mail).
```php
\Mail2::send('emails.reminder', ['user' => $user], function ($m) use ($user) {
    $m->from('hello@app.com', 'Your Application');
    $m->to($user->email, $user->name);
    $m->subject('Your Reminder!');
});
```
