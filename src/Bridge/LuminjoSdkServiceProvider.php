<?php

namespace Luminjo\PhpSdk\Bridge;

use Luminjo\PhpSdk\Luminjo;
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
    }

    public function boot(Application $app)
    {
        // create services
        foreach ($app['luminjo.companies'] as $name => $config) {

            $app['luminjo.'.$name.'.hmac_signature_provider'] = function (Container $container) use ($config) {
                return new HmacSignatureProvider($config['public_key'], $config['private_key'], 'sha1');
            };

            $app['luminjo.'.$name] = function (Container $container) use ($name) {
                return new Luminjo($container['luminjo.'.$name.'.hmac_signature_provider'], $container['luminjo.guzzle.options']);
            };
        }
    }
}
