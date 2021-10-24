<?php

namespace NotificationChannels\WhatsApp;

use Exception;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use JsonSerializable;
use NotificationChannels\WhatsApp\Exceptions\CouldNotSendNotification;
use NotificationChannels\WhatsApp\Traits\HasSharedLogic;
use Psr\Http\Message\ResponseInterface;

class WhatsAppAuth implements JsonSerializable
{
    use HasSharedLogic;

    /**
     * @param int $reconnect
     * @param int $timeout
     * @param string $client_name_long
     * @param string $client_name_short
     *
     * @return static
     */
    public static function create(
        int    $reconnect = 50,
        int    $timeout = 20,
        string $client_name_long = "Laravel WhatsApp Notification Channel",
        string $client_name_short = "Wagorf Worker"
    ): self
    {
        return new static($reconnect, $timeout, $client_name_long, $client_name_short);
    }

    /**
     * Message constructor.
     *
     * @param int $reconnect
     * @param int $timeout
     * @param string $client_short_name
     * @param string $client_long_name
     */
    public function __construct(
        int    $reconnect = 50,
        int    $timeout = 20,
        string $client_short_name = "Wagorf Worker",
        string $client_long_name = "Laravel WhatsApp Notification Channel"
    ) {
        $this->reconnect($reconnect);
        $this->timeout($timeout);
        $this->clientShortName($client_short_name);
        $this->clientLongName($client_long_name);
    }

    /**
     * Reconnect time.
     *
     * @param int $reconnect
     *
     * @return $this
     */
    public function reconnect(int $reconnect): self
    {
        $this->payload['reconnect'] = $reconnect;

        return $this;
    }

    /**
     * QR Scan timeout in second.
     *
     * @param int $timeout
     *
     * @return $this
     */
    public function timeout(int $timeout): self
    {
        $this->payload['timeout'] = $timeout;

        return $this;
    }

    /**
     * QLong client name
     *
     * @param string $client_name_long
     *
     * @return $this
     */
    public function clientLongName(string $client_name_long): self
    {
        $this->payload['client_name_long'] = $client_name_long;

        return $this;
    }

    /**
     * Short client name
     *
     * @param string $client_name_short
     *
     * @return $this
     */
    public function clientShortName(string $client_name_short): self
    {
        $this->payload['client_name_short'] = $client_name_short;

        return $this;
    }

    public function action(string $action): self
    {
        $this->payload['action'] = $action;

        return $this;
    }

    /**
     * @throws CouldNotSendNotification
     */
    public function do(): ?ResponseInterface
    {
        $params = $this->payload;
        $action = $params['action'];
        unset($params['action']);

        if ($action === 'login') {
            return $this->sendRequest('login', $params);
        }

        if ($action === 'logout') {
            return  $this->sendRequest('logout', []);
        }

        return $this->sendRequest('info', []);
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
        $apiUri = sprintf('%s/%s', rtrim(config('services.whatsapp-worker.worker_api_uri'), '/'), $endpoint);

        try {
            if ($endpoint === 'info') {
                return app(HttpClient::class)->get($apiUri);
            }

            return app(HttpClient::class)->post($apiUri, [
                $multipart ? 'multipart' : 'form_params' => $params,
            ]);
        } catch (ClientException $exception) {
            throw CouldNotSendNotification::workerRespondedWithAnError($exception);
        } catch (Exception $exception) {
            throw CouldNotSendNotification::couldNotCommunicateWithWorker($exception->getMessage());
        } catch (GuzzleException $exception) {
            throw CouldNotSendNotification::guzzleHasSomethingToSay($exception);
        }
    }
}
