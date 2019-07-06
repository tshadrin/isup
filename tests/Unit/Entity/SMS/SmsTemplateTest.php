<?php

namespace App\Tests\Unit\Entity\SMS;

use App\Entity\SMS\SmsTemplate;
use PHPUnit\Framework\TestCase;

class SmsTemplateTest extends TestCase
{
    public function testSmsTemplate(): void
    {
        $id = 1;
        $name = "Test";
        $message = "Something message in template sms";

        $smsTemplate = new SmsTemplate();
        $smsTemplate->setId($id);
        self::assertEquals($id, $smsTemplate->getId());
        $smsTemplate->setName($name);
        self::assertEquals($name, $smsTemplate->getName());
        $smsTemplate->setMessage($message);
        self::assertEquals($message, $smsTemplate->getMessage());
        self::assertEquals($name, $smsTemplate->__toString());
    }

}