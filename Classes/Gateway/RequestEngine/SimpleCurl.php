<?php
namespace MKcom\SMS77\Gateway\RequestEngine;

/*
 * This file is part of the MKcom.SMS77 package.
 */

use CodeZero\Curl\CurlException;
use CodeZero\Curl\Request;
use MKcom\SMS77\Gateway\Exception\RequestEngineException;
use MKcom\SMS77\Gateway\RequestEngineInterface;

/**
 * Class SimpleCurl
 *
 * @package MKcom\SMS77\Gateway\RequestEngine
 */
class SimpleCurl implements RequestEngineInterface
{

    /**
     * @param string $url
     * @param array $getParameters
     * @return string response body
     * @throws RequestEngineException
     */
    public function get($url, array $getParameters)
    {
        try {
            $request = new Request();
            $response = $request->get($url, $getParameters);
            $response = $response->getBody();
        } catch (CurlException $e) {
            throw new RequestEngineException('codezero/curl: '.$e->getMessage(), 1475492924);
        }
        return $response;
    }

    /**
     * @param string $url
     * @param array $postParameters
     * @return string response body
     * @throws RequestEngineException
     */
    public function post($url, array $postParameters)
    {
        try {
            $request = new Request();
            $response = $request->post($url, $postParameters);
            $response = $response->getBody();
        } catch (CurlException $e) {
            throw new RequestEngineException('codezero/curl: ' . $e->getMessage(), 1475492976);
        }
        return $response;
    }

}
