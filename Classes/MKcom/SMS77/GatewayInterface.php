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
     * Sends the SMS
     *
     * @param Sms $sms
     * @return Sms
     */
    public function send(Sms $sms);

}
