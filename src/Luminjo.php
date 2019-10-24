<?php

namespace Luminjo\PhpSdk;

use Awelty\Component\Security\HmacSignatureProvider;
use Awelty\Component\Security\MiddlewareProvider;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Client as HttpClient;
use Luminjo\PhpSdk\Client\AuthClient;
use Luminjo\PhpSdk\Client\FaqClient;
use Luminjo\PhpSdk\Client\TicketClient;

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
     * @var FaqClient
     */
    private $faqClient;

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

        if (!isset($guzzleOptions['connect_timeout'])) {
            $guzzleOptions['connect_timeout'] = 3;
        }

        if (!isset($guzzleOptions['timeout'])) {
            $guzzleOptions['timeout'] = 3;
        }

        // set a base_uri if not provided
        if (empty($guzzleOptions['base_uri'])) {
            $guzzleOptions['base_uri'] = self::BASE_URI;
        }

        $this->client = new HttpClient($guzzleOptions);
    }

    public function ticket()
    {
        return $this->ticketClient ?: $this->ticketClient = new TicketClient($this->client);
    }

    public function auth()
    {
        return $this->authClient ?: $this->authClient = new AuthClient($this->client);
    }

    public function faq()
    {
        return $this->faqClient ?: $this->faqClient = new FaqClient($this->client);
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
