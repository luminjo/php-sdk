<?php

namespace Luminjo\PhpSdk\Client;

use GuzzleHttp\Exception\ClientException;
use Luminjo\PhpSdk\LuminjoException;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FaqClient extends AbstractClient
{
    public function index($options = [])
    {
        $optionResolver = new OptionsResolver();

        $optionResolver->setDefaults([
            'category' => null,
        ]);

        $datas = $optionResolver->resolve($options);

        try {
            $response = $this->client->get('/faqs', [
                'query' => $datas,
            ]);
        } catch (ClientException $e) {
            throw new LuminjoException($e->getResponse(), $e);
        }

        return \GuzzleHttp\json_decode($response->getBody()->getContents());

    }

    public function categories()
    {
        try {
            $response = $this->client->get('/faqs_categories');
        } catch (ClientException $e) {
            throw new LuminjoException($e->getResponse(), $e);
        }

        return \GuzzleHttp\json_decode($response->getBody()->getContents());
    }
}
