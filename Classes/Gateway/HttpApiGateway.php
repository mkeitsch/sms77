<?php
namespace MKcom\SMS77\Gateway;

/*
 * This file is part of the MKcom.SMS77 package.
 */

use MKcom\SMS77\Gateway\Exception\Sms77HttpApiGatewayException;
use MKcom\SMS77\GatewayInterface;
use MKcom\SMS77\Sms;

/**
 * Naming usage in this class:
 * HttpApiGateway       => this class
 * Sms77HttpApiGateway  => the real SMS77 http api gateway server
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

    const SMS77_HTTP_API_GATEWAY_DEFAULT_SETTINGS = array(
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
        'username'                   => '',
        'password'                   => '',
        'returnMessageId'            => true,
        'resendLock'                 => true,
        'detailedOutput'             => true,
        'defaultSender'              => '',
        'defaultSmsType'             => Sms::SMS_DELIVERY_TYPE_BASIC,
        'defaultUnicodeTextEncoding' => false,
        'defaultUtf8TextEncoding'    => false,
        'defaultFlashSmsDelivery'    => false,
        'defaultDummySmsDelivery'    => false,
    );

    /**
     * @var static
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
     * Reload lock:
     * If true, the api blocks the delivery of the same SMS within 90 seconds.
     *
     * @var boolean
     */
    protected $resendLock;

    /**
     * @var boolean
     */
    protected $detailedOutput;

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

    /**
     * @param RequestEngineInterface $requestEngine
     * @param array $configuration
     * @return static
     */
    public static function getInstance(RequestEngineInterface $requestEngine, array $configuration = array())
    {
        if (!static::$instance instanceof static) {
            static::$instance = new static($requestEngine, $configuration);
        }
        return static::$instance;
    }

    /**
     * HttpApiGateway constructor.
     *
     * @param RequestEngineInterface $requestEngine
     * @param array $configuration
     */
    public function __construct(RequestEngineInterface $requestEngine, array $configuration = array())
    {
        $this->requestEngine = $requestEngine;
        $this->configuration = array_merge(static::DEFAULT_CONFIGURATION, $configuration);

        foreach ($this->configuration as $key => $value) {
            if (property_exists(static::class, $key)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * @param Sms &$sms
     * @return void
     * @throws Sms77HttpApiGatewayException
     * @throws \Exception
     */
    public function send(Sms &$sms)
    {
        if ($sms->isSent()) {
            return;
        }

        $sms->setSent(true);

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

        $postParameters = array();

        $postParameters['u'] = $this->username;
        $postParameters['p'] = $this->password;

        if ($this->returnMessageId !== static::SMS77_HTTP_API_GATEWAY_DEFAULT_SETTINGS['returnMessageId']) {
            $postParameters['return_msg_id'] = (int)$this->returnMessageId;
        }
        if ($this->resendLock !== static::SMS77_HTTP_API_GATEWAY_DEFAULT_SETTINGS['resendLock']) {
            $postParameters['no_reload'] = (int)$this->resendLock;
        }
        if ($this->detailedOutput !== static::SMS77_HTTP_API_GATEWAY_DEFAULT_SETTINGS['detailedOutput']) {
            $postParameters['details'] = (int)$this->detailedOutput;
        }

        $postParameters['text'] = $sms->getMessage();
        $postParameters['to'] = implode(',', $sms->getRecipients());

        if (!is_null($sms->getDelayedDeliveryTimestamp())) {
            $postParameters['delay'] = $sms->getDelayedDeliveryTimestamp();
        }

        if ($sms->getSender() !== static::SMS77_HTTP_API_GATEWAY_DEFAULT_SETTINGS['defaultSender']) {
            $postParameters['from'] = $sms->getSender();
        }
        if ($sms->getSmsType() !== static::SMS77_HTTP_API_GATEWAY_DEFAULT_SETTINGS['defaultSmsType']) {
            $postParameters['type'] = $sms->getSmsType();
        }
        if ($sms->isUnicodeTextEncoding() !== static::SMS77_HTTP_API_GATEWAY_DEFAULT_SETTINGS['defaultUnicodeTextEncoding']) {
            $postParameters['unicode'] = (int)$sms->isUnicodeTextEncoding();
        }
        if ($sms->isUtf8TextEncoding() !== static::SMS77_HTTP_API_GATEWAY_DEFAULT_SETTINGS['defaultUtf8TextEncoding']) {
            $postParameters['utf8'] = (int)$sms->isUtf8TextEncoding();
        }
        if ($sms->isFlashSms() !== static::SMS77_HTTP_API_GATEWAY_DEFAULT_SETTINGS['defaultFlashSmsDelivery']) {
            $postParameters['flash'] = (int)$sms->isFlashSms();
        }
        if ($sms->isDummySms() !== static::SMS77_HTTP_API_GATEWAY_DEFAULT_SETTINGS['defaultDummySmsDelivery']) {
            $postParameters['debug'] = (int)$sms->isDummySms();
        }

        $responseContent = $this->requestEngine->post($this->smsDeliveryUrl, $postParameters);

        $explodedResponseContent = explode("\n", $responseContent);
        $responseCode = $explodedResponseContent[0];

        if (is_numeric($responseCode)) {
            $responseCode = (int)$responseCode;
            if ($responseCode !== 100) {
                if (in_array($responseCode, static::SMS77_HTTP_API_GATEWAY_STATUS_CODES)) {
                    throw new Sms77HttpApiGatewayException(static::SMS77_HTTP_API_GATEWAY_STATUS_CODES[$responseCode], 1484052369);
                } else {
                    throw new \Exception('Response parsing error! Unknown response code: ' . $responseCode, 1484052396);
                }
            }
        } else {
            throw new \Exception('Response parsing error! Response code is not numeric: ' . var_export($responseCode, TRUE), 1484052172);
        }

        if (isset($explodedResponseContent[1])) {
            $possibleMessageId = $explodedResponseContent[1];
            if (substr($possibleMessageId, 0, 8) != 'Verbucht') {
                if ($this->returnMessageId) {
                    if (is_numeric($possibleMessageId)) {
                        $sms->setMessageId($possibleMessageId);
                    } else {
                        throw new \Exception('Response parsing error! Message ID is not numeric: ' . var_export($possibleMessageId, TRUE), 1484052742);
                    }
                } else {
                    throw new \Exception('Response parsing error! Unexpected value: ' . var_export($possibleMessageId, TRUE), 1484053190);
                }
            }
        }

        $sms->setGatewayResponse($responseContent);
    }

    /**
     * Returns the SMS with the given ID and updates the SMS status
     *
     * @param string|int $messageId
     * @return Sms
     */
    public function getSmsWithLatestStatusById($messageId)
    {
        $sms = new Sms();
        $sms->setMessageId((string)$messageId);
        $this->updateSmsStatus($sms);
        return $sms;
    }

    /**
     * @param Sms &$sms
     * @return void
     * @throws Sms77HttpApiGatewayException
     * @throws \Exception
     */
    public function updateSmsStatus(Sms &$sms)
    {
        $responseContent = $this->requestEngine->get(
            $this->messageStatusUrl,
            array(
                'u' => $this->username,
                'p' => $this->password,
                'msg_id' => $sms->getMessageId(),
            )
        );

        $explodedResponseContent = explode("\n", $responseContent);
        $responseCode = $explodedResponseContent[0];

        if (is_numeric($responseCode)) {
            $responseCode = (int)$responseCode;
            if (in_array($responseCode, static::SMS77_HTTP_API_GATEWAY_STATUS_CODES)) {
                throw new Sms77HttpApiGatewayException(static::SMS77_HTTP_API_GATEWAY_STATUS_CODES[$responseCode], 1475574023);
            } else {
                throw new \Exception('Response parsing error! Unknown response code: ' . $responseCode, 1475574032);
            }
        } else {
            try {
                $sms->setDeliveryStatus($explodedResponseContent[0]);
                $sms->setDeliveryStatusTimestamp($explodedResponseContent[1]);
            } catch (\Exception $e) {
                throw new \Exception("Response parsing error! Error: " . $e->getMessage() . "\nUnexpected response: " . var_export($explodedResponseContent, TRUE), 1484053759);
            }
        }
    }

    /**
     * @return float
     * @throws Sms77HttpApiGatewayException
     */
    public function getCreditStatus()
    {
        $responseContent = $this->requestEngine->get(
            $this->creditStatusUrl,
            array(
                'u' => $this->username,
                'p' => $this->password,
            )
        );

        $responseContent = floatval($responseContent);

        if ($responseContent && intval($responseContent) == $responseContent) {
            if (is_numeric($responseContent) && in_array($responseContent, static::SMS77_HTTP_API_GATEWAY_STATUS_CODES)) {
                throw new Sms77HttpApiGatewayException(static::SMS77_HTTP_API_GATEWAY_STATUS_CODES[$responseContent], 1475484835);
            }
        }

        return (float)$responseContent;
    }

}
