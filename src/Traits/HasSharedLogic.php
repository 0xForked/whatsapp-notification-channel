<?php

namespace NotificationChannels\WhatsApp\Traits;

/**
 * Trait HasSharedLogic.
 */
trait HasSharedLogic
{
    /** @var array Params payload. */
    protected $payload = [];

    /**
     * Recipient's Chat ID.
     *
     * @param string|int $msisdn
     *
     * $msisdn is Destination number.
     * for user e.g. 6281255423
     * or for group e.g. 6281271471566-1619679643
     * (group_creator-timestamp_created)
     *
     * @return $this
     */
    public function to($msisdn): self
    {
        $this->payload['msisdn'] = $msisdn;

        return $this;
    }

    /**
     * Additional options to pass to sendMessage method.
     *
     * @param array $options
     *
     * @return $this
     */
    public function options(array $options): self
    {
        $this->payload = array_merge($this->payload, $options);

        return $this;
    }

    /**
     * Determine if msisdn is not given.
     *
     * @return bool
     */
    public function toNotGiven(): bool
    {
        return ! isset($this->payload['msisdn']);
    }

    /**
     * Get payload value for given key.
     *
     * @param string $key
     *
     * @return mixed|null
     */
    public function getPayloadValue(string $key)
    {
        return $this->payload[$key] ?? null;
    }

    /**
     * Returns params payload.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->payload;
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @return mixed
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
