<?php
namespace MKcom\SMS77\Tests\Unit\Gateway;

/*
 * This file is part of the MKcom.SMS77 package.
 */

use MKcom\SMS77\Gateway\Exception\Sms77HttpApiGatewayException;
use MKcom\SMS77\Gateway\HttpApiGateway;
use MKcom\SMS77\Gateway\RequestEngineInterface;
use MKcom\SMS77\Sms;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * Class HttpApiGatewayTest
 *
 * @package MKcom\SMS77\Tests\Unit\Gateway
 */
class HttpApiGatewayTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var HttpApiGateway
     */
    protected $httpApiGateway;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|RequestEngineInterface
     */
    protected $requestEngine;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|Sms
     */
    protected $sms;

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->sms = $this->createMock(Sms::class);
        $this->sms->method('getRecipients')->willReturn(array());

        $this->requestEngine = $this->createMock(RequestEngineInterface::class);
        $this->httpApiGateway = new HttpApiGateway(
            $this->requestEngine,
            array(
                'username' => 'the username',
                'password' => 'the secret',
            )
        );
    }

    /**
     * @test
     *
     * @return void
     */
    public function aSentSmsWillNotBeSentASecondTime()
    {
        $this->sms->method('isSent')->willReturn(true);
        $this->sms->expects($this->never())->method('setSent');
        $this->httpApiGateway->send($this->sms);
    }

    /**
     * @test
     *
     * @return void
     */
    public function ifNoConfigurationHasBeenSetDefaultSettingsAreUsed()
    {
        $this->httpApiGateway = new HttpApiGateway($this->requestEngine);

        $this->sms->method('getSender')->willReturn(null);
        $this->sms->method('getSmsType')->willReturn(null);
        $this->sms->method('isUnicodeTextEncoding')->willReturn(null);
        $this->sms->method('isUtf8TextEncoding')->willReturn(null);
        $this->sms->method('isFlashSms')->willReturn(null);
        $this->sms->method('isDummySms')->willReturn(null);

        $this->sms->expects($this->once())->method('setSender')->with('');
        $this->sms->expects($this->once())->method('setSmsType')->with('basicplus');
        $this->sms->expects($this->once())->method('setUnicodeTextEncoding')->with(false);
        $this->sms->expects($this->once())->method('setUtf8TextEncoding')->with(false);
        $this->sms->expects($this->once())->method('setFlashSms')->with(false);
        $this->sms->expects($this->once())->method('setDummySms')->with(false);

        $this->requestEngine->method('post')->willReturn("100\n123456\nother fields");
        $this->requestEngine->method('get')->with('https://gateway.sms77.de/status.php')->willReturn("DELIVERED\n123456789");

        $this->httpApiGateway->send($this->sms);
    }

    /**
     * @test
     *
     * @return void
     */
    public function ifSmsValuesHasNotBeenSetConfigurationIsUsed()
    {
        $configuration = array(
            'defaultSender'              => 'I\'m a default sender.',
            'defaultSmsType'             => Sms::SMS_DELIVERY_TYPE_DIRECT,
            'defaultUnicodeTextEncoding' => true,
            'defaultUtf8TextEncoding'    => true,
            'defaultFlashSmsDelivery'    => true,
            'defaultDummySmsDelivery'    => true,
        );

        $this->httpApiGateway = new HttpApiGateway($this->requestEngine, $configuration);

        $this->sms->method('getSender')->willReturn(null);
        $this->sms->method('getSmsType')->willReturn(null);
        $this->sms->method('isUnicodeTextEncoding')->willReturn(null);
        $this->sms->method('isUtf8TextEncoding')->willReturn(null);
        $this->sms->method('isFlashSms')->willReturn(null);
        $this->sms->method('isDummySms')->willReturn(null);

        $this->sms->expects($this->once())->method('setSender')->with($configuration['defaultSender']);
        $this->sms->expects($this->once())->method('setSmsType')->with($configuration['defaultSmsType']);
        $this->sms->expects($this->once())->method('setUnicodeTextEncoding')->with($configuration['defaultUnicodeTextEncoding']);
        $this->sms->expects($this->once())->method('setUtf8TextEncoding')->with($configuration['defaultUtf8TextEncoding']);
        $this->sms->expects($this->once())->method('setFlashSms')->with($configuration['defaultFlashSmsDelivery']);
        $this->sms->expects($this->once())->method('setDummySms')->with($configuration['defaultDummySmsDelivery']);

        $this->requestEngine->method('post')->willReturn("100\n123456\nother fields");
        $this->requestEngine->method('get')->with('https://gateway.sms77.de/status.php')->willReturn("DELIVERED\n123456789");

        $this->httpApiGateway->send($this->sms);
    }

    /**
     * @test
     *
     * @return void
     */
    public function ifSmsValuesHasBeenSetTheseAreUsed()
    {
        $this->httpApiGateway = new HttpApiGateway($this->requestEngine, array(
            'defaultSender'              => 'I\'m a default sender.',
            'defaultSmsType'             => Sms::SMS_DELIVERY_TYPE_DIRECT,
            'defaultUnicodeTextEncoding' => true,
            'defaultUtf8TextEncoding'    => true,
            'defaultFlashSmsDelivery'    => true,
            'defaultDummySmsDelivery'    => true,
        ));

        $this->sms->method('getSender')->willReturn('test sender');
        $this->sms->method('getSmsType')->willReturn('quality');
        $this->sms->method('isUnicodeTextEncoding')->willReturn(false);
        $this->sms->method('isUtf8TextEncoding')->willReturn(false);
        $this->sms->method('isFlashSms')->willReturn(false);
        $this->sms->method('isDummySms')->willReturn(false);

        $this->sms->expects($this->never())->method('setSender');
        $this->sms->expects($this->never())->method('setSmsType');
        $this->sms->expects($this->never())->method('setUnicodeTextEncoding');
        $this->sms->expects($this->never())->method('setUtf8TextEncoding');
        $this->sms->expects($this->never())->method('setFlashSms');
        $this->sms->expects($this->never())->method('setDummySms');

        $this->requestEngine->method('post')->willReturn("100\n123456\nother fields");
        $this->requestEngine->method('get')->with('https://gateway.sms77.de/status.php')->willReturn("DELIVERED\n123456789");

        $this->httpApiGateway->send($this->sms);
    }

    /**
     * @test
     *
     * @return void
     */
    public function postParametersAreNotSetIfDefault()
    {
        $this->sms = $this->createMock(Sms::class);

        $this->httpApiGateway = new HttpApiGateway($this->requestEngine, array(
            'username'        => 'the username',
            'password'        => 'the secret',
            'returnMessageId' => false,
            'resendLock'      => false,
            'detailedOutput'  => false,
        ));

        $this->sms->method('getRecipients')->willReturn(array('0177-555555', 'max', '0177-8888'));
        $this->sms->method('getMessage')->willReturn('test message');

        $this->sms->method('getSender')->willReturn('');
        $this->sms->method('getSmsType')->willReturn('basicplus');
        $this->sms->method('isUnicodeTextEncoding')->willReturn(false);
        $this->sms->method('isUtf8TextEncoding')->willReturn(false);
        $this->sms->method('isFlashSms')->willReturn(false);
        $this->sms->method('isDummySms')->willReturn(false);

        $this->requestEngine->expects($this->once())->method('post')->with('https://gateway.sms77.de/', array(
            'u'             => 'the username',
            'p'             => 'the secret',
            'to'            => '0177-555555,max,0177-8888',
            'text'          => 'test message',
        ))->willReturn("100");

        $this->requestEngine->method('get')->with('https://gateway.sms77.de/status.php')->willReturn("DELIVERED\n123456789");

        $this->httpApiGateway->send($this->sms);
    }

    /**
     * @test
     *
     * @return void
     */
    public function postParametersAreSetIfNotDefault()
    {
        $this->sms = $this->createMock(Sms::class);

        $this->httpApiGateway = new HttpApiGateway($this->requestEngine, array(
            'username'        => 'the username',
            'password'        => 'the secret',
        ));

        $this->sms->method('getRecipients')->willReturn(array('0177-555555', 'max', '0177-8888'));
        $this->sms->method('getMessage')->willReturn('test message');
        $this->sms->method('getDelayedDeliveryTimestamp')->willReturn('123456789');

        $this->sms->method('getSender')->willReturn('test sender');
        $this->sms->method('getSmsType')->willReturn('quality');
        $this->sms->method('isUnicodeTextEncoding')->willReturn(true);
        $this->sms->method('isUtf8TextEncoding')->willReturn(true);
        $this->sms->method('isFlashSms')->willReturn(true);
        $this->sms->method('isDummySms')->willReturn(true);

        $this->requestEngine->expects($this->once())->method('post')->with('https://gateway.sms77.de/', array(
            'u'             => 'the username',
            'p'             => 'the secret',
            'return_msg_id' => 1,
            'no_reload'     => 1,
            'details'       => 1,
            'to'            => '0177-555555,max,0177-8888',
            'text'          => 'test message',
            'delay'         => '123456789',
            'from'          => 'test sender',
            'type'          => 'quality',
            'unicode'       => 1,
            'utf8'          => 1,
            'flash'         => 1,
            'debug'         => 1,
        ))->willReturn("100\n123456\nother fields");

        $this->requestEngine->method('get')->with('https://gateway.sms77.de/status.php')->willReturn("DELIVERED\n123456789");

        $this->httpApiGateway->send($this->sms);
    }

    /**
     * @test
     *
     * @return void
     */
    public function exceptionWillBeThrownIfGatewayStatusNot100OnSend()
    {
        $this->expectException(Sms77HttpApiGatewayException::class);
        $this->expectExceptionMessage('900: Given credentials not valid');
        $this->expectExceptionCode(1484052369);

        $this->requestEngine->method('post')->willReturn('900');

        $this->httpApiGateway->send($this->sms);
    }

    /**
     * @test
     *
     * @return void
     */
    public function returnedCreditStatusIsParsedCorrectlyAsFloat()
    {
        $this->requestEngine->expects($this->once())->method('get')->with('https://gateway.sms77.de/balance.php')->willReturn('45.2745');

        $this->assertEquals(45.2745 ,$this->httpApiGateway->getCreditStatus());
    }

    /**
     * @test
     *
     * @return void
     */
    public function returnedCreditStatusAsErrorCodeThrowsException()
    {
        $this->expectException(Sms77HttpApiGatewayException::class);
        $this->expectExceptionMessage('900: Given credentials not valid');
        $this->expectExceptionCode(1475484835);

        $this->requestEngine->expects($this->once())->method('get')
            ->with('https://gateway.sms77.de/balance.php')
            ->willReturn('900');

        $this->httpApiGateway->getCreditStatus();
    }

    /**
     * @test
     *
     * @return void
     */
    public function exceptionWillBeThrownIfGatewayStatusNot100OnUpdateSmsStatus()
    {
        $this->expectException(Sms77HttpApiGatewayException::class);
        $this->expectExceptionMessage('900: Given credentials not valid');
        $this->expectExceptionCode(1475574023);

        $this->sms->expects($this->once())->method('getMessageId')->willReturn('182734682');

        $this->requestEngine
            ->expects($this->once())
            ->method('get')
            ->with('https://gateway.sms77.de/status.php', array(
                'u' => 'the username',
                'p' => 'the secret',
                'msg_id' => '182734682',
            ))
            ->willReturn('900');

        $this->httpApiGateway->updateSmsStatus($this->sms);
    }

    /**
     * @test
     *
     * @return void
     */
    public function smsStatusWillBeUpdated()
    {
        $this->sms->expects($this->once())->method('getMessageId')->willReturn('182734685');
        $this->sms->expects($this->once())->method('setDeliveryStatus')->with('NOTDELIVERED');
        $this->sms->expects($this->once())->method('setDeliveryStatusTimestamp')->with('987654321');

        $this->requestEngine
            ->expects($this->once())
            ->method('get')
            ->with('https://gateway.sms77.de/status.php', array(
                'u' => 'the username',
                'p' => 'the secret',
                'msg_id' => '182734685',
            ))
            ->willReturn("NOTDELIVERED\n987654321");

        $this->httpApiGateway->updateSmsStatus($this->sms);
    }

}
