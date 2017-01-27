<?php

namespace Luminjo\PhpSdk\Client;

use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketClient extends AbstractClient
{
    /**
     * @param array $options
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
        return $this->serializer->decode($response->getBody(), 'json');
    }
}
