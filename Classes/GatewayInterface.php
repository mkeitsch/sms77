<?php
namespace MKcom\SMS77;

/*
 * This file is part of the MKcom.SMS77 package.
 */

/**
 * Interface GatewayInterface
 *
 * @package MKcom\SMS77
 */
interface GatewayInterface
{

    /**
     * Sends a SMS
     *
     * @param Sms &$sms
     * @return void
     */
    public function send(Sms &$sms);

}
