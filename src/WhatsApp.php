<?php

namespace NotificationChannels\WhatsApp;

use Exception;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use NotificationChannels\WhatsApp\Exceptions\CouldNotSendNotification;
use Psr\Http\Message\ResponseInterface;

/**
 * Class WhatsApp.
 */
class WhatsApp
{
    /** @var HttpClient HTTP Client */
    protected $http;

    /** @var string Worker API Base URI */
    protected $apiBaseUri;

    /**
     * @param HttpClient|null   $httpClient
     * @param string|null $apiBaseUri
     */
    public function __construct(HttpClient $httpClient = null, string $apiBaseUri = null)
    {
        $this->http = $httpClient ?? new HttpClient();

        $this->setApiBaseUri($apiBaseUri);
    }

    /**
     * API Base URI getter.
     *
     * @return string
     */
    public function getApiBaseUri(): string
    {
        return $this->apiBaseUri;
    }

    /**
     * API Base URI setter.
     *
     * @param string $apiBaseUri
     *
     * @return $this
     */
    public function setApiBaseUri(string $apiBaseUri): self
    {
        $this->apiBaseUri = rtrim($apiBaseUri, '/');

        return $this;
    }

    /**
     * Get HttpClient.
     *
     * @return HttpClient
     */
    protected function getHttpClient(): HttpClient
    {
        return $this->http;
    }

    /**
     * Set HTTP Client.
     *
     * @param HttpClient $http
     *
     * @return $this
     */
    public function setHttpClient(HttpClient $http): self
    {
        $this->http = $http;

        return $this;
    }

    /**
     * Send text message.
     *
     * <code>
     * $params = [
     *   'msisdn'               => '',
     *   'text'                 => '',
     *   'msg_quoted_id'        => '',
     *   'msg_quoted'           => '',
     * ];
     * </code>
     *
     * @link https://{{worker_url}}/swagger/index.html#/Messaging/post_v1_whatsapp_send_text
     *
     * @param array $params
     *
     * @throws CouldNotSendNotification
     *
     * @return ResponseInterface|null
     */
    public function sendMessage(array $params): ?ResponseInterface
    {
        return $this->sendRequest('send-text', $params);
    }

    /**
     * Send an API request and return response.
     *
     * @param string $endpoint
     * @param array  $params
     * @param bool   $multipart
     *
     * @throws CouldNotSendNotification
     *
     * @return ResponseInterface|null
     */
    protected function sendRequest(string $endpoint, array $params, bool $multipart = false): ?ResponseInterface
    {
        if (blank($this->apiBaseUri)) {
            throw CouldNotSendNotification::workerUrlNotProvided('You must provide your Worker URL to make any API requests.');
        }

        $apiUri = sprintf('%s/%s', $this->getApiBaseUri(), $endpoint);

        try {
            return $this->getHttpClient()->post($apiUri, [
                $multipart ? 'multipart' : 'form_params' => $params,
            ]);
        } catch (ClientException $exception) {
            throw CouldNotSendNotification::workerRespondedWithAnError($exception);
        } catch (Exception $exception) {
            throw CouldNotSendNotification::couldNotCommunicateWithWorker($exception);
        } catch (GuzzleException $exception) {
            throw CouldNotSendNotification::guzzleHasSomethingToSay($exception);
        }
    }
}
