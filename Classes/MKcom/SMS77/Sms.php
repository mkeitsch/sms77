<?php
namespace MKcom\SMS77;

/*
 * This file is part of the MKcom.SMS77 package.
 */

/**
 * Class Sms
 *
 * @package MKcom\SMS77
 */
class Sms
{

    const SMS_DELIVERY_TYPE_BASIC = 'basicplus';
    const SMS_DELIVERY_TYPE_QUALITY = 'quality';
    const SMS_DELIVERY_TYPE_DIRECT = 'direct';

    const SMS_DELIVERY_STATUS_TRANSMITTED = 'TRANSMITTED';
    const SMS_DELIVERY_STATUS_DELIVERED = 'DELIVERED';
    const SMS_DELIVERY_STATUS_NOT_DELIVERED = 'NOTDELIVERED';
    const SMS_DELIVERY_STATUS_BUFFERED = 'BUFFERED';

    /**
     * @var string
     */
    protected $sender;

    /**
     * @var array
     */
    protected $recipients;

    /**
     * @var string
     */
    protected $message;

    /**
     * @var string
     */
    protected $smsType;

    /**
     * @var int
     */
    protected $delayedDeliveryTimestamp;

    /**
     * @var boolean
     */
    protected $unicodeTextEncoding;

    /**
     * @var boolean
     */
    protected $utf8TextEncoding;

    /**
     * @var boolean
     */
    protected $flashSms;

    /**
     * @var boolean
     */
    protected $dummySms;

    /* ********************[  ]******************** */

    /**
     * @var string
     */
    protected $deliveryStatus;

    /**
     * @var int
     */
    protected $deliveryStatusTimestamp;

    /**
     * @var string
     */
    protected $messageId;

    /**
     * @var boolean
     */
    protected $sent;

    /**
     * @var array
     */
    protected $gatewayResponse;

    /* ********************[ GETTER / SETTER ]******************** */

    /**
     * @return string
     */
    public function getMessageId()
    {
        return $this->messageId;
    }

    /**
     * @param string $messageId
     */
    public function setMessageId($messageId)
    {
        $this->messageId = $messageId;
    }

    /**
     * @return string
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * @param string $sender
     */
    public function setSender($sender)
    {
        $this->sender = $sender;
    }

    /**
     * @return array
     */
    public function getRecipients()
    {
        return $this->recipients;
    }

    /**
     * @param array $recipients
     */
    public function setRecipients($recipients)
    {
        $this->recipients = $recipients;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getSmsType()
    {
        return $this->smsType;
    }

    /**
     * @param string $smsType
     */
    public function setSmsType($smsType)
    {
        $this->smsType = $smsType;
    }

    /**
     * @return int
     */
    public function getDelayedDeliveryTimestamp()
    {
        return $this->delayedDeliveryTimestamp;
    }

    /**
     * @param int $delayedDeliveryTimestamp
     */
    public function setDelayedDeliveryTimestamp($delayedDeliveryTimestamp)
    {
        $this->delayedDeliveryTimestamp = $delayedDeliveryTimestamp;
    }

    /**
     * @return boolean
     */
    public function isUnicodeTextEncoding()
    {
        return $this->unicodeTextEncoding;
    }

    /**
     * @param boolean $unicodeTextEncoding
     */
    public function setUnicodeTextEncoding($unicodeTextEncoding)
    {
        $this->unicodeTextEncoding = $unicodeTextEncoding;
    }

    /**
     * @return boolean
     */
    public function isFlashSms()
    {
        return $this->flashSms;
    }

    /**
     * @param boolean $flashSms
     */
    public function setFlashSms($flashSms)
    {
        $this->flashSms = $flashSms;
    }

    /**
     * @return boolean
     */
    public function isDummySms()
    {
        return $this->dummySms;
    }

    /**
     * @param boolean $dummySms
     */
    public function setDummySms($dummySms)
    {
        $this->dummySms = $dummySms;
    }

    /**
     * @return boolean
     */
    public function isUtf8TextEncoding()
    {
        return $this->utf8TextEncoding;
    }

    /**
     * @param boolean $utf8TextEncoding
     */
    public function setUtf8TextEncoding($utf8TextEncoding)
    {
        $this->utf8TextEncoding = $utf8TextEncoding;
    }

    /**
     * @return string
     */
    public function getDeliveryStatus()
    {
        return $this->deliveryStatus;
    }

    /**
     * @param string $deliveryStatus
     */
    public function setDeliveryStatus($deliveryStatus)
    {
        $this->deliveryStatus = $deliveryStatus;
    }

    /**
     * @return int
     */
    public function getDeliveryStatusTimestamp()
    {
        return $this->deliveryStatusTimestamp;
    }

    /**
     * @param int $deliveryStatusTimestamp
     */
    public function setDeliveryStatusTimestamp($deliveryStatusTimestamp)
    {
        $this->deliveryStatusTimestamp = $deliveryStatusTimestamp;
    }

    /**
     * @return boolean
     */
    public function isSent()
    {
        return $this->sent;
    }

    /**
     * @param boolean $sent
     * @return void
     */
    public function setSent($sent)
    {
        $this->sent = $sent;
    }

    /**
     * @return array
     */
    public function getGatewayResponse()
    {
        return $this->gatewayResponse;
    }

    /**
     * @param array $gatewayResponse
     */
    public function setGatewayResponse($gatewayResponse)
    {
        $this->gatewayResponse = $gatewayResponse;
    }

    /* ********************[ SPECIAL METHODS ]******************** */

    /**
     * @return boolean
     */
    public function isDelivered()
    {
        if ($this->deliveryStatus === self::SMS_DELIVERY_STATUS_DELIVERED) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param GatewayInterface $gateway
     * @return mixed
     */
    public function send(GatewayInterface $gateway)
    {
        return $gateway->send($this);
    }

    public function __clone()
    {
        $this->sent = NULL;
        $this->messageId = NULL;
        $this->deliveryStatus = NULL;
        $this->deliveryStatusTimestamp = NULL;
        $this->gatewayResponse = NULL;
    }

}
