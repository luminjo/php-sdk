<?php

namespace Luminjo\PhpSdk;

use Awelty\Component\Security\HmacSignatureProvider;
use Awelty\Component\Security\MiddlewareProvider;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Client as HttpClient;
use Luminjo\PhpSdk\Client\AuthClient;
use Luminjo\PhpSdk\Client\TicketClient;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;

/**
 * Luminjo API client
 */
class Luminjo
{
    const BASE_URI = 'https://api.luminjo.com';

    /**
     * @var HttpClient
     */
    private $client;

    /**
     * @var TicketClient
     */
    private $ticketClient;

    /**
     * @var AuthClient
     */
    private $authClient;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * Client constructor.
     * @param $publicKey
     * @param $privateKey
     * @param array $guzzleOptions
     */
    public function __construct($publicKey, $privateKey, $guzzleOptions = [])
    {
        $hmacSignature = new HmacSignatureProvider($publicKey, $privateKey, 'sha1');

        $handler = !empty($guzzleOptions['handler']) ? $guzzleOptions['handler'] : HandlerStack::create();
        $handler->push(MiddlewareProvider::signRequestMiddleware($hmacSignature));

        $guzzleOptions['handler'] = $handler;

        // set a base_uri if not provided
        if (empty($guzzleOptions['base_uri'])) {
            $guzzleOptions['base_uri'] = self::BASE_URI;
        }

        $this->client = new HttpClient($guzzleOptions);

        $this->serializer = new Serializer([], [new JsonEncoder()]);
    }

    public function ticket()
    {
        return $this->ticketClient ?: $this->ticketClient = new TicketClient($this->client, $this->serializer);
    }

    public function auth()
    {
        return $this->authClient ?: $this->authClient = new AuthClient($this->client, $this->serializer);
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     * @deprecated
     */
    public function helloWorld()
    {
        @trigger_error('helloWorld() is deprecated, use auth->verify instead.', E_USER_DEPRECATED);
        return $this->client->get('/');
    }
}
