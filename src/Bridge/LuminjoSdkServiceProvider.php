<?php

namespace Luminjo\PhpSdk\Bridge;

use Luminjo\PhpSdk\Luminjo;
use Awelty\Component\Security\HmacAuthenticator;
use Awelty\Component\Security\HmacSignatureProvider;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Api\BootableProviderInterface;
use Silex\Application;

class LuminjoSdkServiceProvider implements ServiceProviderInterface, BootableProviderInterface
{
    public function register(Container $container)
    {
        /**
         * Les applications EmStorage
         */
        $container['luminjo.companies'] = [];

        /**
         * Options pour construire le client Guzzle
         */
        $container['luminjo.guzzle.options'] = [
            'debug' => false,
        ];

        /**
         * Authenticator
         */
        $container['luminjo.hmac_authenticator'] = function (Container $container) {
            return new HmacAuthenticator('sha1');
        };
    }

    public function boot(Application $app)
    {
        // create services
        foreach ($app['luminjo.companies'] as $name => $config) {
            $authenticator = new HmacSignatureProvider($config['public_key'], $config['private_key'], 'sha1');

            $app['luminjo.'.$name] = function (Container $container) use ($authenticator) {
                return new Luminjo($authenticator, $container['luminjo.guzzle.options']);
            };
        }
    }
}
