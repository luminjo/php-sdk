# Centre Aide PHP SDK

PHP SDK for http://www.centre-aide.fr

## Install

```
composer require centre-aide/php-sdk:dev-master
```
A stable version will eventually be released one day...

### Create a client

#### With Silex

You can use the service provider: 

```
<?php 

CentreAide\PhpSdk\Bridge\CentreAideSdkServiceProvider;

$app->register(new CentreAideSdkServiceProvider(), [
    'centreaide.companies' => [
        'yourCompanyName' => [
            'public_key' => 'appPublicKey',
            'private_key' => 'appPrivateKey',
        ]
    ],
    // optionnal, guzzle client __construct options
    'centreaide.guzzle.options' => [
        'debug' => false,
    ]
]);
```

For each companies it will create a service named "centreaide.*yourCompanyName*"

#### Or manually

```
<?php 

use Awelty\Component\Security\HmacSignatureProvider;
use CentreAide\PhpSdk\Client;

// Centreaide use hmac authentification with sha1 as algo
$signatureProvider = new HmacSignatureProvider($publicKey, $privateKey, 'sha1');

$centreAide = new Client($authenticator, $someGuzzleConfig = []);
```

### Usage

- Create a ticket 
```
<?php 


    /**
     * @param string $fromEmail the user e-mail
     * @param string $subject
     * @param string $content
     * @param string|null $url the current url, or the url the ticket is about
     * @param string|null $userAgent
     * @param string|null $navigator the navigator JS object (as json)
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function createTicket($fromEmail, $subject, $content, $url = null, $userAgent = null, $navigator = null);
```
