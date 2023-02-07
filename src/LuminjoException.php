<?php

namespace Luminjo\PhpSdk;

use GuzzleHttp\Psr7\Response;

class LuminjoException extends \Exception
{
    protected $psrResponse;

    public function __construct(Response $prsResponse, \Exception $previous = null)
    {
        $this->psrResponse = $prsResponse;
        $message = '';
        $code = 0;

        $contents = $prsResponse->getBody()->getContents();

        if (!$contents) {
            parent::__construct($message, $code, $previous);
            return;
        }

        try {
            $datas = \GuzzleHttp\json_decode($contents);
        } catch (\InvalidArgumentException $e) {
            parent::__construct($message, $code, $previous);
            return;
        }

        // gestion des multiples possibilités
        // erreur de formulaire
        if (is_array($datas) && isset($datas[0]) && is_object($datas[0]) && isset($datas[0]->message)) {
            $message = $datas[0]->message;
        }

        if (is_object($datas) && isset($datas->error)) {
            $message = $datas->error->message;
            $code = $datas->error->code;
        }

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->psrResponse;
    }
}
