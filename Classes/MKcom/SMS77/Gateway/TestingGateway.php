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
     * @var TestingGateway
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
     * @return TestingGateway
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * GatewayService constructor.
     */
    public function __construct()
    {
        if (self::$instance === null) {
            self::$instance = $this;
        }
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
     */
    public function setGatewayResponse($gatewayResponse)
    {
        $this->gatewayResponse = (string)$gatewayResponse;
    }

    /**
     * @param Sms $sms
     * @return Sms
     */
    public function send(Sms $sms)
    {
        if (empty($this->deliveryStatus) || ! in_array($this->deliveryStatus, self::AVAILABLE_DELIVERY_STATUSES)) {
            $this->deliveryStatus = Sms::SMS_DELIVERY_STATUS_DELIVERED;
        }

        $sms->setSent(true);
        $sms->setDeliveryStatusTimestamp((new \DateTime())->getTimestamp());
        $sms->setDeliveryStatus($this->deliveryStatus);
        $sms->setMessageId(uniqid());
        $sms->setGatewayResponse($this->gatewayResponse);

        return $sms;
    }

}
