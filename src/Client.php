<?php

namespace CentreAide\PhpSdk;

use Awelty\Component\Security\HmacSignatureProvider;
use Awelty\Component\Security\MiddlewareProvider;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Client as HttpClient;

/**
 * Centre-aide API client
 */
class Client
{
    const BASE_URI = 'https://api.centre-aide.fr';

    private $client;

    /**
     * Client constructor.
     * @param HmacSignatureProvider $hmacSignature
     * @param array $guzzleOptions
     */
    public function __construct(HmacSignatureProvider $hmacSignature, $guzzleOptions = [])
    {
        $handler = !empty($guzzleOptions['handler']) ? $guzzleOptions['handler'] : HandlerStack::create();
        $handler->push(MiddlewareProvider::signRequestMiddleware($hmacSignature));

        $guzzleOptions['handler'] = $handler;

        // set a base_uri if not provided
        if (empty($guzzleOptions['base_uri'])) {
            $guzzleOptions['base_uri'] = self::BASE_URI;
        }

        $this->client = new HttpClient($guzzleOptions);
    }

    /**
     * @param string $fromEmail the user e-mail
     * @param string $subject
     * @param string $content
     * @param string|null $url the current url, or the url the ticket is about
     * @param string|null $userAgent
     * @param string|null $navigator the navigator JS object (as json)
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function createTicket($fromEmail, $subject, $content, $url = null, $userAgent = null, $navigator = null)
    {
        return $this->client->post('/tickets', [
            'json' => [
                'ticket' => [
                    'user' => $fromEmail,
                    'subject' => $subject,
                    'content' => $content,
                    'url' => $url,
                    'userAgent' => $userAgent,
                    'navigator' => $navigator,
                ]
            ]
        ]);
    }
}
