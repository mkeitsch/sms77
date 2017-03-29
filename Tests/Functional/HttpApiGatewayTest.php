<?php
namespace MKcom\SMS77\Tests\Functional;

/*
 * This file is part of the MKcom.SMS77 package.
 */

use MKcom\SMS77\Gateway\HttpApiGateway;
use MKcom\SMS77\Gateway\RequestEngine\SimpleCurl;
use MKcom\SMS77\Gateway\RequestEngineInterface;
use MKcom\SMS77\Sms;
use PHPUnit_Framework_TestCase;

/**
 * Class HttpApiGatewayTest
 *
 * @package MKcom\SMS77\Tests\Functional
 */
class HttpApiGatewayTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var HttpApiGateway
     */
    protected $httpApiGateway;

    /**
     * @var RequestEngineInterface
     */
    protected $requestEngine;

    /**
     * @var Sms
     */
    protected $sms;

    /**
     * @test
     *
     * @return void
     */
    public function sendDummySmsToLiveServer()
    {
        // Read credentials from file
        {
            $path = __DIR__ . DIRECTORY_SEPARATOR . 'credentials.txt';
            if (!file_exists($path)) {
                $this->markTestIncomplete('To use this test, please create a file named "credentials.txt" in the same directory of this test file with your SMS77 API credentials formatted like: username:password');
            }
            $fileContent = file_get_contents($path);
            $fileContent = explode("\n", $fileContent);
            $credentials = explode(":", $fileContent[0]);
            $username = $credentials[0];
            $password = $credentials[1];
        }

        $sms = new Sms();
        $sms->setMessage("This is a test message. Greetings...");
        $sms->addRecipient('1234567890');
        $sms->setDummySms(TRUE);

        $gateway = HttpApiGateway::getInstance(
            SimpleCurl::getInstance(),
            array(
                'username'                   => $username,
                'password'                   => $password,
                'defaultDummySmsDelivery'    => TRUE,
            )
        );
        $gateway->send($sms);
    }

}
