<?php
namespace MKcom\SMS77\Gateway;

/*
 * This file is part of the MKcom.SMS77 package.
 */

/**
 * Interface RequestEngineInterface
 *
 * @package MKcom\SMS77\Gateway
 */
interface RequestEngineInterface
{

    /**
     * @param string $url
     * @param array $getParameters
     * @return string response body
     */
    public function get($url, array $getParameters);

    /**
     * @param string $url
     * @param array $postParameters
     * @return string response body
     */
    public function post($url, array $postParameters);

}
