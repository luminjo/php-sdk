<?php

namespace Luminjo\PhpSdk;

use GuzzleHttp\Psr7\Response;

class LuminjoException extends \Exception
{
    protected $psrResponse;

    public function __construct(Response $prsResponse, \Exception $previous = null)
    {
        $this->psrResponse = $prsResponse;
        $message = null;
        $code = 0;

        if ($contents = $prsResponse->getBody()->getContents()) {
            try {
                $datas = \GuzzleHttp\json_decode($contents);
                $message = $datas->error->message;
                $code = $datas->error->code;
            } catch (\InvalidArgumentException $e) {
                // nothing
            }
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
