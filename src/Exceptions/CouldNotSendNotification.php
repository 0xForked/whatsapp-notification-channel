<?php

namespace NotificationChannels\WhatsApp\Exceptions;

use Exception;
use GuzzleHttp\Exception\ClientException;

class CouldNotSendNotification extends Exception
{
    /**
     * Thrown when there's a bad request and an error is responded.
     *
     * @param ClientException $exception
     *
     * @return static
     */
    public static function workerRespondedWithAnError(ClientException $exception): self
    {
        if (! $exception->hasResponse()) {
            return new static('Worker responded with an error but no response body found');
        }

        $statusCode = $exception->getResponse()->getStatusCode();

        $result = json_decode($exception->getResponse()->getBody(), false);
        $description = $result->description ?? 'no description given';

        return new static("Worker responded with an error `{$statusCode} - {$description}`", 0, $exception);
    }

    /**
     * Thrown when there's no worker url provided.
     *
     * @param string $message
     *
     * @return static
     */
    public static function workerUrlNotProvided(string $message): self
    {
        return new static($message);
    }

    /**
     * Thrown when we're unable to communicate with Worker.
     *
     * @param $message
     *
     * @return static
     */
    public static function couldNotCommunicateWithWorker($message): self
    {
        return new static("The communication with worker failed. `{$message}`");
    }

    /**
     * Thrown when we're guzzle has something to say.
     *
     * @param $message
     *
     * @return static
     */
    public static function guzzleHasSomethingToSay($message): self
    {
        return new static("Error with guzzle. `{$message}`");
    }
}
