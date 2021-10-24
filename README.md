# WhatsApp Notifications Channel for Laravel using [wagorf](https://github.com/aasumitro/wagorf) as a Worker

[![Latest Version on Packagist](https://img.shields.io/packagist/v/laravel-notification-channels/:package_name.svg?style=flat-square)](https://packagist.org/packages/laravel-notification-channels/:package_name)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/laravel-notification-channels/:package_name/master.svg?style=flat-square)](https://travis-ci.org/laravel-notification-channels/:package_name)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/:sensio_labs_id.svg?style=flat-square)](https://insight.sensiolabs.com/projects/:sensio_labs_id)
[![Quality Score](https://img.shields.io/scrutinizer/g/laravel-notification-channels/:package_name.svg?style=flat-square)](https://scrutinizer-ci.com/g/laravel-notification-channels/:package_name)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/laravel-notification-channels/:package_name/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/laravel-notification-channels/:package_name/?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/laravel-notification-channels/:package_name.svg?style=flat-square)](https://packagist.org/packages/laravel-notification-channels/:package_name)

This package makes it easy to send notifications using [WhatsApp Bussiness API](https://www.whatsapp.com/business/api) over wagorf Worker with Laravel 8.0+

## Contents

- [Installation](#installation)
	- [Setting up the WhatsApp Worker](#setting-up-the-:service_name-service)
- [Usage](#usage)
	- [Available Message methods](#available-message-methods)
- [Changelog](#changelog)
- [Testing](#testing)
- [Security](#security)
- [Contributing](#contributing)
- [Credits](#credits)
- [License](#license)


## Installation

You can install the package via composer:

`composer require aasumitro/whatsapp-notification-channel`

### Setting up the WhatsApp Worker

Then, configure your WhatsApp Worker API URL:

```php
// config/services.php
'whatsapp-worker' => [
    'worker_api_uri' => env('WAGORF_API_URL', 'YOUR BOT TOKEN HERE')
],
```

### Register service provider

Then, register whatsapp notification service provider:
```php
// config/services.php

'providers' => [
    ....
    
    \NotificationChannels\WhatsApp\WhatsAppServiceProvider::class
],
```

## Usage
You can now use the channel in your via() method inside the Notification class.

```php
<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WhatsApp\WhatsAppChannel;
use NotificationChannels\WhatsApp\WhatsAppMessage;

class WhatsappSendTextMessageNotification extends Notification
{
    use Queueable;

    private $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }

    public function via($notifiable): array
    {
        return [WhatsAppChannel::class];
    }

    public function toWhatsapp($notifiable): WhatsAppMessage
    {
        return WhatsAppMessage::create()
            ->to($notifiable->msisdn)
            ->content($this->message);
    }
}
```

### Routing a Message

You can either send the notification by providing with the Destination number of the recipient to the to($msisdn) method like shown in the previous examples or add a routeNotificationForWhatsApp() method in your notifiable model:

```php 
/**
 * Route notifications for the WhatsApp channel.
 *
 * @return int
 */
public function routeNotificationForWhatsApp()
{
    return $this->whatsapp_phone_number;
}
```

### Available Message methods

## 1.0.0 - 2021-10-24

- WhatsAppAuth

  available methods:
    - `->action($action)` : (string) selection between [login]|[logout]|[info]
    - `->do()`: Running this action

  usages:
    ```php
        \NotificationChannels\WhatsApp\WhatsAppAuth::create()
        ->action('info') // info/login/logout
        ->do();
    ```

- WhatsAppMessage

  available methods:
    - `->to($msisdn)`: (string) Recipient's Destination number
    - `->content('')`: (string) Notification message.
    - `->options([])`: (array) Allows you to add additional or override sendMessage payload.
- Helpers

  available methods:
    - `extract_message($message, $needle): object` (object) Will extract response message or something

for more please read Changelog

#### TODO NEXT
1. WhatsAppFile (audio, document, image, video) ***prior***
2. WhatsAppLocation

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Security

If you discover any security related issues, please email [hello@aasumitro.id](mailto:hello@aasumitro.id) instead of using the issue tracker.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [A. A. Sumitro](https://github.com/aasumitro)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
