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

    const SMS_TYPE_BASIC = 'basic';
    const SMS_TYPE_BASICPLUS = 'basicplus';
    const SMS_TYPE_QUALITY = 'quality';
    const SMS_TYPE_DIRECT = 'direct';

    const SMS_STATUS_TRANSMITTED = 'transmitted';
    const SMS_STATUS_DELIVERED = 'delivered';
    const SMS_STATUS_NOT_DELIVERED = 'not delivered';
    const SMS_STATUS_BUFFERED = 'buffered';

    /**
     * @var string
     */
    protected $messageId;

    /**
     * @var string
     */
    protected $sender;

    /**
     * @var array()
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
    protected $unicodeSms;

    /**
     * @var boolean
     */
    protected $flashSms;

    /**
     * @var boolean
     */
    protected $dummySms;

    /**
     * @var boolean
     */
    protected $utf8;

    /**
     * @var string
     */
    protected $status;

    /**
     * @var int
     */
    protected $statusTimestamp;

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
     * @return void
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
     * @return void
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
     * @return void
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
     * @return void
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
     * @return void
     */
    public function setDelayedDeliveryTimestamp($delayedDeliveryTimestamp)
    {
        $this->delayedDeliveryTimestamp = $delayedDeliveryTimestamp;
    }

    /**
     * @return boolean
     */
    public function isUnicodeSms()
    {
        return $this->unicodeSms;
    }

    /**
     * @param boolean $unicodeSms
     * @return void
     */
    public function setUnicodeSms($unicodeSms)
    {
        $this->unicodeSms = $unicodeSms;
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
     * @return void
     */
    public function setDummySms($dummySms)
    {
        $this->dummySms = $dummySms;
    }

    /**
     * @return boolean
     */
    public function isUtf8()
    {
        return $this->utf8;
    }

    /**
     * @param boolean $utf8
     * @return void
     */
    public function setUtf8($utf8)
    {
        $this->utf8 = $utf8;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return void
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return int
     */
    public function getStatusTimestamp()
    {
        return $this->statusTimestamp;
    }

    /**
     * @param int $statusTimestamp
     * @return void
     */
    public function setStatusTimestamp($statusTimestamp)
    {
        $this->statusTimestamp = $statusTimestamp;
    }

    /**
     * @return boolean
     */
    public function isDelivered()
    {
        if ($this->status === self::SMS_STATUS_DELIVERED) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return mixed
     */
    public function send()
    {
        $gatewayService = GatewayService::getInstance();
        return $gatewayService->sendSms($this);
    }

}