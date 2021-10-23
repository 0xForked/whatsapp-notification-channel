<?php

namespace NotificationChannels\WhatsApp;

use JsonSerializable;
use NotificationChannels\WhatsApp\Traits\HasSharedLogic;

class WhatsAppMessage implements JsonSerializable
{
    use HasSharedLogic;

    /**
     * @param string $content
     *
     * @return self
     */
    public static function create(string $content = ''): self
    {
        return new self($content);
    }

    /**
     * Message constructor.
     *
     * @param string $content
     */
    public function __construct(string $content = '')
    {
        $this->content($content);
    }

    /**
     * Notification message (Supports Markdown).
     *
     * @param string $content
     *
     * @return $this
     */
    public function content(string $content): self
    {
        $this->payload['text'] = $content;

        return $this;
    }
}
