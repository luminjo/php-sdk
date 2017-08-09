# Luminjo PHP SDK

PHP SDK for https://fr.luminjo.com

## Install

```
composer require luminjo/php-sdk:dev-master
```
A stable version will eventually be released one day...

## Create a client

### With Silex

You can use the service provider: 

```
<?php 

Luminjo\PhpSdk\Bridge\LuminjoSdkServiceProvider;

$app->register(new LuminjoSdkServiceProvider(), [
    'luminjos' => [
        'yourCompanyName' => [
            'public_key' => 'appPublicKey',
            'private_key' => 'appPrivateKey',
            'options' => [  // optionnal, guzzle client __construct options
                'debug' => false,
            ]
        ]
    ],
]);
```

For each companies it will create a service named "luminjo.*yourCompanyName*"

### Or manually

```
<?php 

use Luminjo\PhpSdk\Luminjo;

$luminjo = new Luminjo($publicKey, $privateKey, $guzzleOptions = []);
```

## Usage

- Verify that you are able to make API requests
```
<?php 

use Luminjo\PhpSdk\LuminjoException;

    // ...

    try {
        // eveything is ok
        $luminjo->auth()->verify();
    } catch (LuminjoException $e) {
        $code = $e->getCode(); // see error handling
        $message = $e->getMessage();
         
        // the PSR response can be retrieved
        $originalResponse = $e->getResponse();
    }
    
```

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
        'folder' => 'My folder', // a folder name, will be created if missing
        'tags' => ['tag 1', 'tag 2'], // some tags
        'files' => [
            [
                'filename' => 'test avatar api.jpg', // display usage 
                'path' => 'path/to/file', // a fopen-able path
            ]
        ],
        'extra_fields' => [ // values MUST be scalar  
            'a' => 'b',
            'any' => 'key-value pair'
        ]
    ]);
    
    // 201 empty response
    $ticketUrl = $response->getHeader('Location');
```

- find tickets
```
<?php
   $tickets = $luminjo->ticket()->find([
   
       // required fields
       'email' => 'client@email.com',
   ]);
 
```

You wille receive an array response : 
 
```
Array
(
    [0] => stdClass Object
        (
            [subject] => My ticket // can be null
            [url] => https://posao.luminjo.com/tickets/123
            [type] => 1 // see "Ticket types" chapter
        )

)

```

- get faq (behavior is the same as on the website : index get highlited questions)
 
```
<?php
   $questions = $luminjo->faq()->index([
       // optionnal 
       'category' => $categoryId,
   ]);
```

Response : 
 
```
Array
(
    [0] => stdClass Object
        (
            [id] => 1
            [qustion] => "How change my password"
            [response] => "<p>Seriously ?</p>" // html content
            [highlight] => true / false
        )

)
```

- Faq categories

```
<?php
   $categories = $luminjo->faq()->categories();
```

Response : 
 
```
Array
(
    [0] => stdClass Object
        (
            [id] => 1
            [name] => "My category"
        )
)
```

### Ticket types

Tickets can be more than simple tickets, for example when you get a customer call. Remember, only tickets type 1 should be visible for a customer, other types are hidden.
- ticket: 1
- call: 2
- meet: 3
- other: 4

## Error handling

Every API call must be try catched to handle client errors. The LuminjoException contain the response, the error code and a message. 
The message is for the developper, the code can help you to show a proper error message to the end user. 

```
<?php 

use Luminjo\PhpSdk\LuminjoException;

    // ...

    try {
        $response = $luminjo->whatever();
    } catch (LuminjoException $e) {
        $code = $e->getCode();
        $message = $e->getMessage();
        $originalResponse = $e->getResponse();
    }
    
```

### Codes 
 

- 1: Rate limit exceeded. You are doing too many requests, you should wait 60 secondes before the next. 
- 2: Not allowed. Your current plan doesn't allow you to use the API.
- 3: Authentification failed. The API keys are probably wrong. 

Keep in mind that codes can be added in the future.

### Limits

Current limitation is 60 requests per minutes.

## Options

You can find options ($guzzleOptions) here: http://docs.guzzlephp.org/en/latest/request-options.html

If you don't provide these keys, these are the default: 
- connect_timeout: 3
- timeout: 3
