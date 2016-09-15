<?php

namespace CentreAide\PhpSdk\Bridge;

use CentreAide\PhpSdk\Client;
use Awelty\Component\Security\HmacAuthenticator;
use Awelty\Component\Security\HmacSignatureProvider;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Api\BootableProviderInterface;
use Silex\Application;

class CentreAideSdkServiceProvider implements ServiceProviderInterface, BootableProviderInterface
{
    public function register(Container $container)
    {
        /**
         * Les applications EmStorage
         */
        $container['centreaide.companies'] = [];

        /**
         * Options pour construire le client Guzzle
         */
        $container['centreaide.guzzle.options'] = [
            'debug' => false,
            'base_uri' => 'https://api.centre-aide.fr',
        ];

        /**
         * Authenticator
         */
        $container['centreaide.hmac_authenticator'] = function (Container $container) {
            return new HmacAuthenticator('sha1');
        };
    }

    public function boot(Application $app)
    {
        // create services
        foreach ($app['centreaide.companies'] as $name => $config) {
            $authenticator = new HmacSignatureProvider($config['public_key'], $config['private_key'], 'sha1');

            $app['centreaide.'.$name.'.client'] = function (Container $container) use ($authenticator) {
                return new Client($authenticator, $container['centreaide.guzzle.options']);
            };
        }
    }
}
