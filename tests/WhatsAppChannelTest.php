<?php

namespace NotificationChannels\WhatsApp\Test;

use GuzzleHttp\Psr7\Response;
use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Mockery;
use Illuminate\Contracts\Events\Dispatcher;
use NotificationChannels\WhatsApp\Exceptions\CouldNotSendNotification;
use NotificationChannels\WhatsApp\WhatsApp;
use NotificationChannels\WhatsApp\WhatsAppChannel;
use NotificationChannels\WhatsApp\WhatsAppMessage;
use PHPUnit\Framework\TestCase;

class WhatsAppChannelTest extends TestCase
{
    /** @var Mockery\Mock */
    protected $whatsapp;

    /** @var WhatsappChannel */
    protected $channel;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $dispatcher;

    public function setUp(): void
    {
        parent::setUp();
        $this->whatsapp = Mockery::mock(WhatsApp::class);
        $this->dispatcher = $this->createMock(Dispatcher::class);
        $this->channel = new WhatsAppChannel($this->whatsapp, $this->dispatcher);
    }

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function itCanSendAMessage(): void
    {
        $expectedResponse = [
            'ok' => true,
            'result' => [
                'code' => 200,
                'status' => 'ok',
                'data' => [
                    'message_id' => 123
                ]
            ]
        ];

        $this->whatsapp->shouldReceive('sendMessage')->once()->with([
            'text' => 'Laravel Notification Channels are awesome!',
            'msisdn' => 12345,
        ])->andReturns(new Response(200, [], json_encode($expectedResponse)));

        $actualResponse = $this->channel->send(new TestNotifiable(), new TestNotification());

        self::assertSame($expectedResponse, $actualResponse);
    }

    /**
     * @test
     */
    public function notificationFailedEvent(): void
    {
        self::expectException($exception_class = CouldNotSendNotification::class);
        self::expectExceptionMessage($exception_message = 'Some exception');

        $notifiable = new TestNotifiable();
        $notification = new TestNotification();

        $this->whatsapp
            ->shouldReceive('sendMessage')
            ->andThrow($exception_class, $exception_message);

        $this->dispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with(
                new NotificationFailed(
                    $notifiable,
                    $notification,
                    'whatsapp',
                    []
                )
            )
        ;

        $this->channel->send($notifiable, $notification);
    }
}


/**
 * Class TestNotifiable.
 */
class TestNotifiable
{
    use Notifiable;

    public function routeNotificationForWhatsapp(): int
    {
        return false;
    }
}

/**
 * Class TestNotification.
 */
class TestNotification extends Notification
{
    /**
     * @param $notifiable
     */
    public function toWhatsapp($notifiable): WhatsAppMessage
    {
        return WhatsAppMessage::create('Laravel Notification Channels are awesome!')->to(12345);
    }
}
