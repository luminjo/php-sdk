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
        $container['luminjos'] = [];
    }

    public function boot(Application $app)
    {
        // create services
        foreach ($app['luminjos'] as $name => $config) {
            if (!isset($config['options'])) {
                $config['options'] = [];
            }

            $app['luminjo.'.$name] = function (Container $container) use ($config) {
                return new Luminjo($config['public_key'], $config['private_key'], $config['options']);
            };
        }
    }
}
