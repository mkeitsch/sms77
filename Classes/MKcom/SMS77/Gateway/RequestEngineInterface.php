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
     * @param $url
     * @param $getParameters
     * @return string Response Body
     */
    public function get($url, $getParameters);

    /**
     * @param $url
     * @param $postParameters
     * @return string Response Body
     */
    public function post($url, $postParameters);

}
