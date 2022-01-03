<?php

namespace NotificationChannels\WhatsApp\Test;

use NotificationChannels\WhatsApp\WhatsAppMessage;
use PHPUnit\Framework\TestCase;

class WhatsAppMessageTest extends TestCase
{
    /** @test */
    public function itAcceptsContentWhenConstructed(): void
    {
        $message = new WhatsAppMessage('Laravel Notification Channels are awesome!');
        $this->assertEquals('Laravel Notification Channels are awesome!', $message->getPayloadValue('text'));
    }

    /** @test */
    public function theRecipientsMsisdnCanBeSet(): void
    {
        $message = new WhatsAppMessage();
        $message->to(12345);
        $this->assertEquals(12345, $message->getPayloadValue('msisdn'));
    }

    /** @test */
    public function theNotificationMessageCanBeSet(): void
    {
        $message = new WhatsAppMessage();
        $message->content('Laravel Notification Channels are awesome!');
        $this->assertEquals('Laravel Notification Channels are awesome!', $message->getPayloadValue('text'));
    }

    /** @test */
    public function itCanDetermineIfTheRecipientMsisdnHasNotBeenSet(): void
    {
        $message = new WhatsAppMessage();
        $this->assertTrue($message->toNotGiven());

        $message->to(12345);
        $this->assertFalse($message->toNotGiven());
    }

    /** @test */
    public function itCanReturnThePayloadAsAnArray(): void
    {
        $message = new WhatsAppMessage('Laravel Notification Channels are awesome!');
        $message->to(12345);
        $expected = [
            'text' => 'Laravel Notification Channels are awesome!',
            'msisdn' => 12345,
        ];

        $this->assertEquals($expected, $message->toArray());
    }
}
