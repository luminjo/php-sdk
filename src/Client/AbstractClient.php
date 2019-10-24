<?php

namespace Luminjo\PhpSdk\Client;

use GuzzleHttp\Client;

class AbstractClient
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * ApplicationClient constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }
}
