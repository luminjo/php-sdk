# Luminjo PHP SDK

PHP SDK for https://fr.luminjo.com

## Install

```
composer require luminjo/php-sdk:dev-master
```
A stable version will eventually be released one day...

### Create a client

#### With Silex

You can use the service provider: 

```
<?php 

Luminjo\PhpSdk\Bridge\LuminjoSdkServiceProvider;

$app->register(new LuminjoSdkServiceProvider(), [
    'luminjo.companies' => [
        'yourCompanyName' => [
            'public_key' => 'appPublicKey',
            'private_key' => 'appPrivateKey',
        ]
    ],
    // optionnal, guzzle client __construct options
    'luminjo.guzzle.options' => [
        'debug' => false,
    ]
]);
```

For each companies it will create a service named "luminjo.*yourCompanyName*"

#### Or manually

```
<?php 

use Awelty\Component\Security\HmacSignatureProvider;
use Luminjo\PhpSdk\Luminjo;

// Luminjo use hmac authentification with sha1 as algo
$signatureProvider = new HmacSignatureProvider($publicKey, $privateKey, 'sha1');

$luminjo = new Luminjo($authenticator, $someGuzzleConfig = []);
```

### Usage

- Create a ticket 
```
<?php 

    $response = $luminjo->ticket()->create([
    
        // required fields
        'from' => 'client@email.com',
        'subject' => '$subject',
        'content' => '$htmlContent',
        
        // optionnal
        'url' => 'http://www.google.com', // a related url or whatever you want..
        'user_agent' => $userAgent, 
        'tags' => ['tag 1', 'tag 2'], // some tags
        'files' => [
            [
                'filename' => 'test avatar api.jpg', // display usage 
                'path' => 'path/to/file', // a fopen-able path
            ]
        ]
    ]);
    
    // 201 empty response
    $ticketUrl = $response->getHeader('Location');
```
