<?php
declare(strict_types=1);

namespace App\Form\SMS;


use App\Entity\SMS\SmsTemplate;
use Symfony\Component\Validator\Constraints as Assert;

class SmsTemplateDTO
{
    /**
     * @var string
     * @Assert\NotNull()
     * @Assert\Length(min="10", max="10")
     * @Assert\Regex(pattern="/^9\d{9}/"))
     */
    public $phone;

    /** @var int */
    public $utmId;

    /** @var SmsTemplate */
    public $smsTemplate;

    public static function create(int $utmId, string $phone): self
    {
        $smsTemplateDTO = new self;
        $smsTemplateDTO->utmId = $utmId;
        $smsTemplateDTO->phone = $phone;
        return $smsTemplateDTO;
    }
}