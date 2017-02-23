<?php

namespace Luminjo\PhpSdk\Client;

use Psr\Http\Message\ResponseInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketClient extends AbstractClient
{
    /**
     * @param array $options
     * @return ResponseInterface
     */
    public function create($options)
    {
        $optionResolver = new OptionsResolver();
        $optionResolver->setRequired(['from', 'subject', 'content']);

        $optionResolver->setDefaults([
            'url' => null,
            'user_agent' => null,
            'navigator' => null,
            'tags' => [],
            'files' => [],
            'extra_fields' => [],
        ]);

        $ticket = $optionResolver->resolve($options);

        $multipart = [
            ['name' => 'ticket[author]', 'contents' => $ticket['from']],
            ['name' => 'ticket[subject]', 'contents' => $ticket['subject']],
            ['name' => 'ticket[content]', 'contents' => $ticket['content']],
            ['name' => 'ticket[url]', 'contents' => $ticket['url']],
            ['name' => 'ticket[userAgent]', 'contents' => $ticket['user_agent']],
            ['name' => 'ticket[navigator]', 'contents' => $ticket['navigator']],
        ];

        foreach ($ticket['extra_fields'] as $k => $v) {
            if (!is_scalar($v)) {
                throw new \Exception(sprintf('All "extra_fields" must be scalar, "%s" value is not.', $k));
            }
        }

        if ($ticket['extra_fields']) {
            $multipart[] = ['name' => 'ticket[extraFields]', 'contents' => serialize($ticket['extra_fields'])];
        }

        foreach ($ticket['tags'] as $tag) {
            $multipart[] = ['name' => 'ticket[tags][]', 'contents' => $tag];
        }

        foreach ($ticket['files'] as $i => $file) {
            $multipart[] = [
                'name' => 'ticket[attachments]['.$i.'][uploadedFile]',
                'contents' => fopen($file['path'], 'r'),
                'filename' => $file['filename']
            ];
        }

        $response = $this->client->post('/tickets', [
            'multipart' => $multipart
        ]);

        return $response;
//        return $this->serializer->decode($response->getBody(), 'json');
    }
}
