<?php

namespace Awelty\CentreAide\PhpSdk;

use Awelty\Component\Security\AuthenticatorInterface;
use Awelty\Component\Security\Middleware;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Client as HttpClient;

class Client
{
    /**
     * @var AuthenticatorInterface
     */
    private $authenticator;

    private $client;

    public function __construct(AuthenticatorInterface $authenticator, $guzzleOptions = [])
    {
        $this->authenticator = $authenticator;

        // Création du handler
        //---------------------
        $handler = HandlerStack::create();
        $handler->push(Middleware::authenticateMiddleware($authenticator));

        if (isset($guzzleOptions['handler'])) {
            throw new \Exception('Do you really need to set an handler ?');
        }

        $guzzleOptions['handler'] = $handler;

        // Création du client
        //-------------------
        $this->client = new HttpClient($guzzleOptions);
    }

}
