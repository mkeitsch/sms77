<?php
namespace MKcom\SMS77\Gateway;

/*
 * This file is part of the MKcom.SMS77 package.
 */

use MKcom\SMS77\Gateway\Exception\Sms77HttpApiGatewayException;
use MKcom\SMS77\GatewayInterface;
use MKcom\SMS77\Sms;

/**
 * Name usage:
 * HttpApiGateway is this class
 * Sms77HttpApiGateway is the real SMS77 http api gateway server
 *
 * Class HttpApiGateway
 *
 * @package MKcom\SMS77\Gateway
 */
class HttpApiGateway implements GatewayInterface
{

    const SMS77_HTTP_API_GATEWAY_STATUS_CODES = array(
        100 => '100: SMS delivered successfully',
        101 => '101: Delivery to at least one recipient failed',
        201 => '201: Sender illegal', // max. 11 alpha-numeric or 16 numeric ciphers
        202 => '202: Recipient number illegal',
        300 => '300: Missing username or password',
        301 => '301: Argument "to" missing',
        304 => '304: Argument "type" missing',
        305 => '305: Argument "text" missing',
        306 => '306: Sender number illegal',
        307 => '307: Argument "url" missing',
        400 => '400: Argument "type" invalid',
        401 => '401: Argument "text" too long',
        402 => '402: Reload lock - same SMS sent within 90 seconds',
        500 => '500: Not enough credits',
        600 => '600: Carrier delivery failed',
        700 => '700: Unknown error',
        801 => '801: Logo file not set',
        802 => '802: Logo file does not exist',
        803 => '803: Ring tone not set',
        900 => '900: Given credentials not valid',
        901 => '901: Message ID invalid',
        902 => '902: HTTP API not activated for this account',
        903 => '903: Server IP invalid',
        11  => '11: SMS carrier temporarily not available',
        0   => 'Error: Status code does not exist'
    );

    const SMS77_HTTP_API_GATEWAY_DEFAULTS = array(
        'returnMessageId'            => false,
        'resendLock'                 => false,
        'detailedOutput'             => false,
        'defaultSender'              => '',
        'defaultSmsType'             => Sms::SMS_DELIVERY_TYPE_BASIC,
        'defaultUnicodeTextEncoding' => false,
        'defaultUtf8TextEncoding'    => false,
        'defaultFlashSmsDelivery'    => false,
        'defaultDummySmsDelivery'    => false,
    );

    const DEFAULT_CONFIGURATION = array(
        'smsDeliveryUrl'             => 'https://gateway.sms77.de/',
        'creditStatusUrl'            => 'https://gateway.sms77.de/balance.php',
        'messageStatusUrl'           => 'https://gateway.sms77.de/status.php',
        // Gateway Settings
        'username'                   => '',
        'password'                   => '',
        'returnMessageId'            => true,
        'resendLock'                 => true,
        'detailedOutput'             => true,
        // SMS Defaults
        'defaultSender'              => '',
        'defaultSmsType'             => Sms::SMS_DELIVERY_TYPE_BASIC,
        'defaultUnicodeTextEncoding' => false,
        'defaultUtf8TextEncoding'    => false,
        'defaultFlashSmsDelivery'    => false,
        'defaultDummySmsDelivery'    => false,
    );

    /* ********************[ GATEWAY INTERNAL FIELDS ]******************** */

    /**
     * @var HttpApiGateway
     */
    protected static $instance;

    /**
     * @var array
     */
    protected $configuration;

    /**
     * @var RequestEngineInterface
     */
    protected $requestEngine;

    /* ********************[ GATEWAY SETTINGS ]******************** */

    /**
     * @var string
     */
    protected $smsDeliveryUrl;

    /**
     * @var string
     */
    protected $creditStatusUrl;

    /**
     * @var string
     */
    protected $messageStatusUrl;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var boolean
     */
    protected $returnMessageId;

    /**
     * Reload lock: The api blocks the delivery of the same SMS within 90 seconds.
     *
     * @var boolean
     */
    protected $resendLock;

    /**
     * @var boolean
     */
    protected $detailedOutput;

    /* ********************[ SMS DEFAULTS ]******************** */

    /**
     * @var string
     */
    protected $defaultSender;

    /**
     * @var string
     */
    protected $defaultSmsType;

    /**
     * @var boolean
     */
    protected $defaultUnicodeTextEncoding;

    /**
     * @var boolean
     */
    protected $defaultUtf8TextEncoding;

    /**
     * @var boolean
     */
    protected $defaultFlashSmsDelivery;

    /**
     * @var boolean
     */
    protected $defaultDummySmsDelivery;

    /* ********************[ METHODS ]******************** */

    /**
     * @param RequestEngineInterface $requestEngine
     * @param array $configuration
     * @return HttpApiGateway
     * @throws \Exception
     */
    public static function getInstance(RequestEngineInterface $requestEngine = null, $configuration = array())
    {
        if (self::$instance === NULL) {
            if (!$requestEngine instanceof RequestEngineInterface) {
                throw new \Exception('RequestEngine required', 1475494771);
            }
            self::$instance = new self($requestEngine, $configuration);
        }
        return self::$instance;
    }

    /**
     * HttpApiGateway constructor.
     *
     * @param RequestEngineInterface $requestEngine
     * @param array $configuration
     */
    public function __construct(RequestEngineInterface $requestEngine, $configuration = array())
    {
        if (self::$instance === NULL) {
            self::$instance = $this;
        }

        $this->requestEngine = $requestEngine;
        $this->configuration = array_merge(self::DEFAULT_CONFIGURATION, $configuration);

        foreach ($this->configuration as $key => $value) {
            if (property_exists(self::class, $key)) {
                $this->$key = $value;
            }
        }
    }

    /* ********************[ GATEWAY SERVICE METHODS ]******************** */

    /**
     * @param Sms $sms
     * @return Sms
     * @throws Sms77HttpApiGatewayException
     * @throws \Exception
     */
    public function send(Sms $sms)
    {
        if ($sms->isSent()) {
            return $sms;
        }

        $sms->setSent(true);

        // Set configuration

        if (is_null($sms->getSender())) {
            $sms->setSender($this->defaultSender);
        }
        if (is_null($sms->getSmsType())) {
            $sms->setSmsType($this->defaultSmsType);
        }
        if (is_null($sms->isUnicodeTextEncoding())) {
            $sms->setUnicodeTextEncoding($this->defaultUnicodeTextEncoding);
        }
        if (is_null($sms->isUtf8TextEncoding())) {
            $sms->setUtf8TextEncoding($this->defaultUtf8TextEncoding);
        }
        if (is_null($sms->isFlashSms())) {
            $sms->setFlashSms($this->defaultFlashSmsDelivery);
        }
        if (is_null($sms->isDummySms())) {
            $sms->setDummySms($this->defaultDummySmsDelivery);
        }

        // Set URL parameters

        $postParameters = array();

        $postParameters['u'] = $this->username;
        $postParameters['p'] = $this->password;


        if ($this->returnMessageId !== self::SMS77_HTTP_API_GATEWAY_DEFAULTS['returnMessageId']) {
            $postParameters['return_msg_id'] = (int)$this->returnMessageId;
        }
        if ($this->resendLock !== self::SMS77_HTTP_API_GATEWAY_DEFAULTS['resendLock']) {
            $postParameters['no_reload'] = (int)$this->resendLock;
        }
        if ($this->detailedOutput !== self::SMS77_HTTP_API_GATEWAY_DEFAULTS['detailedOutput']) {
            $postParameters['details'] = (int)$this->detailedOutput;
        }


        if (!is_array($sms->getRecipients())) {
            $sms->setRecipients(array());
        }

        $postParameters['text'] = $sms->getMessage();
        $postParameters['to'] = implode(',', $sms->getRecipients());

        if (!is_null($sms->getDelayedDeliveryTimestamp())) {
            $postParameters['delay'] = $sms->getDelayedDeliveryTimestamp();
        }


        if ($sms->getSender() !== self::SMS77_HTTP_API_GATEWAY_DEFAULTS['defaultSender']) {
            $postParameters['from'] = $sms->getSender();
        }
        if ($sms->getSmsType() !== self::SMS77_HTTP_API_GATEWAY_DEFAULTS['defaultSmsType']) {
            $postParameters['type'] = $sms->getSmsType();
        }
        if ($sms->isUnicodeTextEncoding() !== self::SMS77_HTTP_API_GATEWAY_DEFAULTS['defaultUnicodeTextEncoding']) {
            $postParameters['unicode'] = (int)$sms->isUnicodeTextEncoding();
        }
        if ($sms->isUtf8TextEncoding() !== self::SMS77_HTTP_API_GATEWAY_DEFAULTS['defaultUtf8TextEncoding']) {
            $postParameters['utf8'] = (int)$sms->isUtf8TextEncoding();
        }
        if ($sms->isFlashSms() !== self::SMS77_HTTP_API_GATEWAY_DEFAULTS['defaultFlashSmsDelivery']) {
            $postParameters['flash'] = (int)$sms->isFlashSms();
        }
        if ($sms->isDummySms() !== self::SMS77_HTTP_API_GATEWAY_DEFAULTS['defaultDummySmsDelivery']) {
            $postParameters['debug'] = (int)$sms->isDummySms();
        }

        // Send request

        $responseContent = $this->requestEngine->post($this->smsDeliveryUrl, $postParameters);

        // Parse response

        if (is_numeric($responseContent)) {
            $responseContent = (int) $responseContent;
            if ($responseContent !== 100 && in_array($responseContent, self::SMS77_HTTP_API_GATEWAY_STATUS_CODES)) {
                throw new Sms77HttpApiGatewayException(self::SMS77_HTTP_API_GATEWAY_STATUS_CODES[$responseContent], 1475485924);
            }
        } elseif (strpos($responseContent, "\n") !== false) {
            if ($this->returnMessageId) {
                $responseContentExploded = explode("\n", $responseContent);
                $sms->setMessageId($responseContentExploded[1]);

                // Get current delivery status

                try {
                    $sms = $this->getMessageStatus($sms);
                } catch (Sms77HttpApiGatewayException $e) {}
            }
        } else {
            throw new \Exception('Response parsing error', 1475572670);
        }

        $sms->setGatewayResponse($responseContent);

        return $sms;
    }


    /**
     * @return float
     * @throws Sms77HttpApiGatewayException
     */
    public function getCreditStatus()
    {
        $responseContent = $this->requestEngine->get($this->creditStatusUrl, array(
            'u' => $this->username,
            'p' => $this->password,
        ));

        // Check if the returned value is float (credit status) or int (status code)

        $responseContent = floatval($responseContent);

        if ($responseContent && intval($responseContent) == $responseContent) {
            if (is_numeric($responseContent) && in_array($responseContent, self::SMS77_HTTP_API_GATEWAY_STATUS_CODES)) {
                throw new Sms77HttpApiGatewayException(self::SMS77_HTTP_API_GATEWAY_STATUS_CODES[$responseContent], 1475484835);
            }
        }

        return (float)$responseContent;
    }

    /**
     * @param Sms|string $message SMS object or message id
     * @return Sms
     * @throws Sms77HttpApiGatewayException
     * @throws \Exception
     */
    public function getMessageStatus($message)
    {
        if ($message instanceof Sms) {
            $messageId = $message->getMessageId();
        } else {
            $messageId = $message;
        }

        $responseContent = $this->requestEngine->get($this->messageStatusUrl, array(
            'u' => $this->username,
            'p' => $this->password,
            'msg_id' => $messageId,
        ));

        if (is_numeric($responseContent)) {
            $responseContent = (int)$responseContent;
            if ($responseContent !== 100 && in_array($responseContent, self::SMS77_HTTP_API_GATEWAY_STATUS_CODES)) {
                throw new Sms77HttpApiGatewayException(self::SMS77_HTTP_API_GATEWAY_STATUS_CODES[$responseContent], 1475574023);
            } else {
                throw new \Exception('Response parsing error', 1475574032);
            }
        } elseif (strpos($responseContent, "\n") !== false) {
            $responseContentExploded = explode("\n", $responseContent);

            $deliveryStatus = $responseContentExploded[0];
            $deliveryStatusTimestamp = $responseContentExploded[1];
        } else {
            throw new \Exception('Response parsing error', 1475574038);
        }

        if (! $message instanceof Sms) {
            $message = new Sms();
            $message->setMessageId($messageId);
        }

        $message->setDeliveryStatus($deliveryStatus);
        $message->setDeliveryStatusTimestamp($deliveryStatusTimestamp);

        return $message;
    }

}
