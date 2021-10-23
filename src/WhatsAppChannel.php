<?php

namespace NotificationChannels\WhatsApp;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Notifications\Notification;
use NotificationChannels\WhatsApp\Exceptions\CouldNotSendNotification;

class WhatsAppChannel
{
    /**
     * @var WhatsApp
     */
    protected $whatsapp;

    /**
     * @var Dispatcher
     */
    private $dispatcher;

    public function __construct(WhatsApp $whatsapp, Dispatcher $dispatcher)
    {
        $this->whatsapp = $whatsapp;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Send the given notification.
     *
     * @param mixed        $notifiable
     * @param Notification $notification
     *
     * @throws CouldNotSendNotification
     * @return null|array
     */
    public function send($notifiable, Notification $notification): ?array
    {
         $message = $notification->toWhatsapp($notifiable);

         if (is_string($message)) {
             $message = WhatsAppMessage::create($message);
         }

         if ($message->toNotGiven()) {
             if (! $to = $notifiable->routeNotificationForWhatsApp('msisdn', $notification)) {
                 return null;
             }

             $message->to($to);
         }

         $params = $message->toArray();

         try{
            if ($message instanceof WhatsAppMessage) {
                $response = $this->whatsapp->sendMessage($params);
            } else {
                return null;
            }
        } catch (CouldNotSendNotification $exception) {
            $this->dispatcher->dispatch(new NotificationFailed(
                $notifiable,
                $notification,
                'whatsapp',
                []
            ));

            throw $exception;
        }


        return json_decode($response->getBody()->getContents(), true);
    }
}
