<?php

namespace Awelty\CentreAide\PhpSdk;

use Awelty\Component\Security\HmacSignatureProvider;
use Awelty\Component\Security\MiddlewareProvider;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Client as HttpClient;

class Client
{
    private $client;

    public function __construct(HmacSignatureProvider $hmacSignature, $guzzleOptions = [])
    {
        // Création du handler
        //---------------------
        $handler = HandlerStack::create();
        $handler->push(MiddlewareProvider::signRequestMiddleware($hmacSignature));

        if (isset($guzzleOptions['handler'])) {
            throw new \Exception('Do you really need to set an handler ?');
        }

        $guzzleOptions['handler'] = $handler;

        // Création du client
        //-------------------
        $this->client = new HttpClient($guzzleOptions);
    }

}
