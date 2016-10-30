<?php
namespace MKcom\SMS77\Gateway;

/*
 * This file is part of the MKcom.SMS77 package.
 */

use MKcom\SMS77\GatewayInterface;
use MKcom\SMS77\Sms;

/**
 * Class TestingGateway
 *
 * @package MKcom\SMS77\Gateway
 */
class TestingGateway implements GatewayInterface
{

    const AVAILABLE_DELIVERY_STATUSES = array(
        Sms::SMS_DELIVERY_STATUS_TRANSMITTED,
        Sms::SMS_DELIVERY_STATUS_DELIVERED,
        Sms::SMS_DELIVERY_STATUS_NOT_DELIVERED,
        Sms::SMS_DELIVERY_STATUS_BUFFERED
    );

    /**
     * @var static
     */
    protected static $instance;

    /**
     * @var string
     */
    protected $deliveryStatus;

    /**
     * @var string
     */
    protected $gatewayResponse;

    /**
     * @return static
     */
    public static function getInstance()
    {
        if (!static::$instance instanceof static) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * @param string $deliveryStatus
     * @return void
     */
    public function setDeliveryStatus($deliveryStatus)
    {
        $this->deliveryStatus = (string)$deliveryStatus;
    }

    /**
     * @param string $gatewayResponse
     * @return void
     */
    public function setGatewayResponse($gatewayResponse)
    {
        $this->gatewayResponse = (string)$gatewayResponse;
    }

    /**
     * @param Sms &$sms
     * @return void
     */
    public function send(Sms &$sms)
    {
        if (empty($this->deliveryStatus) || !in_array($this->deliveryStatus, static::AVAILABLE_DELIVERY_STATUSES)) {
            $this->deliveryStatus = Sms::SMS_DELIVERY_STATUS_DELIVERED;
        }

        if (empty($this->gatewayResponse)) {
            $this->gatewayResponse = '100';
        }

        $sms->setSent(true);
        $sms->setDeliveryStatusTimestamp((new \DateTime())->getTimestamp());
        $sms->setDeliveryStatus($this->deliveryStatus);
        $sms->setMessageId(uniqid());
        $sms->setGatewayResponse($this->gatewayResponse);
    }

}
