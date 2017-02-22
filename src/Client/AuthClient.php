<?php

namespace Luminjo\PhpSdk\Client;

use GuzzleHttp\Exception\ClientException;

class AuthClient extends AbstractClient
{
    public function verify()
    {
//        try {
            $response = $this->client->get('/auth/verify');
//        } catch (ClientException $e) {
//            $response = $e->getResponse();
//        }

        return $response;
    }
}
