<?php

namespace Awelty\CentreAide\PhpSdk\Bridge;

use Awelty\CentreAide\PhpSdk\Client;
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
        $container['centreaide.options'] = [];

        /**
         * Authenticator
         */
        $container['centreaide.hmac_authenticator'] = function (Container $container) {
            return new HmacAuthenticator('sha1');
        };
    }

    public function boot(Application $app)
    {
        // "force" base_uri
        $options = $app['guzzle.options'];
        $options['base_uri'] = 'http://api.jplantey.centre-aide.fr'; // TODO

        $app['guzzle.options'] = $options;

        // create services
        foreach ($app['centreaide.companies'] as $name => $config) {
            $authenticator = new HmacSignatureProvider($config['public_key'], $config['private_key'], 'sha1');

            $app['centreaide.'.$name.'.client'] = function (Container $container) use ($authenticator) {
                return new Client($authenticator, $container['guzzle.options']);
            };
        }
    }
}
