<?php
namespace MKcom\SMS77;

/*
 * This file is part of the MKcom.SMS77 package.
 */

/**
 * Class GatewayService
 *
 * @package MKcom\SMS77
 */
class GatewayService
{


    const STATUS_CODE_100 = 'SMS delivered successfully';
    const STATUS_CODE_101 = 'Delivery to at least one recipient failed';
    const STATUS_CODE_201 = 'Sender illegal'; // max. 11 alpha-numeric or 16 numeric ciphers
    const STATUS_CODE_202 = 'Recipient number illegal';
    const STATUS_CODE_300 = 'Missing username or password';
    const STATUS_CODE_301 = 'Argument "to" missing';
    const STATUS_CODE_304 = 'Argument "type" missing';
    const STATUS_CODE_305 = 'Argument "text" missing';
    const STATUS_CODE_306 = 'Sender number illegal';
    const STATUS_CODE_307 = 'Argument "url" missing';
    const STATUS_CODE_400 = 'Argument "type" invalid';
    const STATUS_CODE_401 = 'Argument "text" too long';
    const STATUS_CODE_402 = 'Reload lock - same SMS sent within 90 seconds';
    const STATUS_CODE_500 = 'Not enough credits';
    const STATUS_CODE_600 = 'Carrier delivery failed';
    const STATUS_CODE_700 = 'Unknown error';
    const STATUS_CODE_801 = 'Logo file not set';
    const STATUS_CODE_802 = 'Logo file does not exist';
    const STATUS_CODE_803 = 'Ring tone not set';
    const STATUS_CODE_900 = 'Given credentials not valid';
    const STATUS_CODE_902 = 'HTTP API not activated for this account';
    const STATUS_CODE_903 = 'Server IP invalid';
    const STATUS_CODE_11 = 'SMS carrier temporarily not available';

    /**
     * @var GatewayService
     */
    private static $instance;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var string
     */
    protected $defaultSmsType = Sms::SMS_TYPE_BASIC;

    /**
     * @var boolean
     */
    protected $defaultFlashSmsDelivery = false;

    /**
     * @var string
     */
    protected $defaultSender = '';

    /**
     * @var boolean
     */
    protected $defaultDummySmsDelivery = false;

    /**
     * @var string
     */
    protected $returnMessageId = true;

    /**
     * Reload lock: The api blocks the delivery of the same SMS within 90 seconds.
     *
     * @var boolean
     */
    protected $resendLock = true;

    /**
     * @var boolean
     */
    protected $defaultUnicodeDelivery = false;

    /**
     * @var boolean
     */
    protected $defaultUtf8Delivery = false;

    /**
     * @var boolean
     */
    protected $detailedOutput = true;

    /**
     * @var int
     */
    protected $lastStatusCode;

    /**
     * @var string
     */
    protected $lastStatusMessage;

    /**
     * @param array $configuration
     * @return GatewayService
     */
    public static function getInstance($configuration)
    {
        if (self::$instance === NULL) {
            self::$instance = new self($configuration);
        }
        return self::$instance;
    }

    /**
     * GatewayService constructor.
     *
     * @param array $configuration
     */
    public function __construct($configuration)
    {
        if (self::$instance === NULL) {
            self::$instance = $this;
        }
    }

    public function sendSms()
    {
        // TODO
    }

    /**
     * @return float
     */
    public function getCreditStatus()
    {
        // TODO
    }

    /**
     * @param string|Sms $message
     * @return Sms
     */
    public function getMessageStatus($message)
    {

        if ($message instanceof Sms) {
            $messageId = $message->getMessageId();
        } else {
            $messageId = $message;
        }

        // TODO

        $status = '';
        $statusTimestamp = 1;

        // TODO

        if (! $message instanceof Sms) {
            $message = new Sms();
            $message->setMessageId($messageId);
        }

        $message->setStatus($status);
        $message->setStatusTimestamp($statusTimestamp);

        return $message;
    }

    public function getGatewayStatus()
    {

    }

}