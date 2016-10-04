<?php
namespace MKcom\SMS77\Tests\Unit;

/*
 * This file is part of the MKcom.SMS77 package.
 */

use PHPUnit_Framework_TestCase;
use MKcom\SMS77\Sms;

class SmsTest extends PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function smsCanBeCloned()
    {
        $newSms = new Sms();

        $timestamp = (new \DateTime())->getTimestamp();

        $newSms->setMessage('This is a test message.');
        $newSms->setRecipients(array('Recipient#1'));
        $newSms->setDummySms(true);

        $newSms->setMessageId('123456');
        $newSms->setSent(true);
        $newSms->setDeliveryStatus(Sms::SMS_DELIVERY_STATUS_BUFFERED);
        $newSms->setDeliveryStatusTimestamp($timestamp);
        $newSms->setGatewayResponse('SMS has been sent');

        $clonedSms = clone $newSms;

        $this->assertEquals('This is a test message.', $clonedSms->getMessage());
        $this->assertContains('Recipient#1', $clonedSms->getRecipients());
        $this->assertTrue($clonedSms->isDummySms());

        $this->assertNull($clonedSms->getMessageId());
        $this->assertNull($clonedSms->getDeliveryStatus());
        $this->assertNull($clonedSms->getDeliveryStatusTimestamp());
        $this->assertNull($clonedSms->getGatewayResponse());
        $this->assertNull($clonedSms->isSent());
    }

}
