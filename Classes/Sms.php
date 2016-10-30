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
     * @var array<string>
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
     * @var string
     */
    protected $gatewayResponse;

    /**
     * @return string
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * @param string $sender
     * @return void
     */
    public function setSender($sender)
    {
        $this->sender = (string)$sender;
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
     * @return void
     */
    public function setRecipients(array $recipients)
    {
        $this->recipients = $recipients;
    }

    /**
     * @param $recipient
     * @return void
     */
    public function addRecipient($recipient)
    {
        $this->recipients[] = (string)$recipient;
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
     * @return void
     */
    public function setMessage($message)
    {
        $this->message = (string)$message;
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
     * @return void
     */
    public function setSmsType($smsType)
    {
        $this->smsType = (string)$smsType;
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
     * @return void
     */
    public function setDelayedDeliveryTimestamp($delayedDeliveryTimestamp)
    {
        $this->delayedDeliveryTimestamp = (int)$delayedDeliveryTimestamp;
    }

    /**
     * @param \DateTime $delayedDeliveryDateTime
     * @return void
     */
    public function setDelayedDeliveryDateTime(\DateTime $delayedDeliveryDateTime)
    {
        $this->delayedDeliveryTimestamp = $delayedDeliveryDateTime->getTimestamp();
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
     * @return void
     */
    public function setUnicodeTextEncoding($unicodeTextEncoding)
    {
        $this->unicodeTextEncoding = (boolean)$unicodeTextEncoding;
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
     * @return void
     */
    public function setFlashSms($flashSms)
    {
        $this->flashSms = (boolean)$flashSms;
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
     * @return void
     */
    public function setDummySms($dummySms)
    {
        $this->dummySms = (boolean)$dummySms;
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
     * @return void
     */
    public function setUtf8TextEncoding($utf8TextEncoding)
    {
        $this->utf8TextEncoding = (boolean)$utf8TextEncoding;
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
     * @return void
     */
    public function setDeliveryStatus($deliveryStatus)
    {
        $this->deliveryStatus = (string)$deliveryStatus;
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
     * @return void
     */
    public function setDeliveryStatusTimestamp($deliveryStatusTimestamp)
    {
        $this->deliveryStatusTimestamp = (int)$deliveryStatusTimestamp;
    }

    /**
     * @param \DateTime $deliveryStatusDateTime
     * @return void
     */
    public function setDeliveryStatusDateTime(\DateTime $deliveryStatusDateTime)
    {
        $this->deliveryStatusTimestamp = $deliveryStatusDateTime->getTimestamp();
    }

    /**
     * @return string
     */
    public function getMessageId()
    {
        return $this->messageId;
    }

    /**
     * @param string $messageId
     * @return void
     */
    public function setMessageId($messageId)
    {
        $this->messageId = (string)$messageId;
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
        $this->sent = (boolean)$sent;
    }

    /**
     * @return string
     */
    public function getGatewayResponse()
    {
        return $this->gatewayResponse;
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
     * @return Sms
     */
    public function send(GatewayInterface $gateway)
    {
        return $gateway->send($this);
    }

    /**
     * @return void
     */
    public function __clone()
    {
        $this->sent = NULL;
        $this->messageId = NULL;
        $this->deliveryStatus = NULL;
        $this->deliveryStatusTimestamp = NULL;
        $this->gatewayResponse = NULL;
    }

}
