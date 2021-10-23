# Changelog

All notable changes to `whatsapp-notification-channel` will be documented in this file

## 1.0.0 - 2021-10-24

- initial release
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
