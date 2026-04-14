<?php

namespace Luminjo\PhpSdk\Security;

use Psr\Http\Message\RequestInterface;

class HmacSignatureProvider implements SignatureProviderInterface
{
    /**
     * @var string
     */
    private $publicKey;

    /**
     * @var string
     */
    private $privateKey;

    /**
     * @var string
     */
    private $algo;

    /**
     * @param string $publicKey
     * @param string $privateKey
     * @param string $algo md5, sha1, ...
     */
    public function __construct($publicKey, $privateKey, $algo)
    {
        $this->publicKey = $publicKey;
        $this->privateKey = $privateKey;
        $this->algo = $algo;
    }

    /**
     * @param RequestInterface $request
     * @return RequestInterface
     */
    public function sign(RequestInterface $request)
    {
        foreach ($this->getHeaders($request->getMethod(), $request->getRequestTarget()) as $k => $v) {
            $request = $request->withHeader($k, $v);
        }

        return $request;
    }

    /**
     * @param string $method
     * @param string $requestTarget
     * @return array
     */
    public function getHeaders($method, $requestTarget)
    {
        $now = new \DateTime();
        $plainSignature = $method . urldecode($requestTarget) . $now->format(\DateTime::ISO8601);

        return [
            'X-Public-Key' => $this->publicKey,
            'X-Datetime' => $now->format(\DateTime::ISO8601),
            'X-Signature' => hash_hmac($this->algo, $plainSignature, $this->privateKey),
        ];
    }
}
