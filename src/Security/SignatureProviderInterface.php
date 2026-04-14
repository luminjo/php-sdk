<?php

namespace Luminjo\PhpSdk\Security;

use Psr\Http\Message\RequestInterface;

interface SignatureProviderInterface
{
    /**
     * Sign a request to authenticate
     * @param RequestInterface $request
     * @return RequestInterface
     */
    public function sign(RequestInterface $request);
}
