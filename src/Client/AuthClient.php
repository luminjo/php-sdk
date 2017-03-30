<?php

namespace Luminjo\PhpSdk\Client;

use GuzzleHttp\Exception\ClientException;
use Luminjo\PhpSdk\LuminjoException;
use Symfony\Component\HttpFoundation\Response;

class AuthClient extends AbstractClient
{
    /**
     * @return \Psr\Http\Message\ResponseInterface
     * @throws LuminjoException
     */
    public function verify()
    {
        try {
            $response = $this->client->get('/auth/verify');
        } catch (ClientException $e) {
            throw new LuminjoException($e->getResponse(), $e);
        }

        return $response;
//        return new Response($response->getBody(), $response->getStatusCode(), $response->getHeaders());
    }
}
