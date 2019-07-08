<?php
declare(strict_types=1);

namespace App\Form\SMS;

use App\Entity\SMS\SmsTemplate;
use Symfony\Component\Validator\Constraints as Assert;

class SmsTemplateData
{
    /**
     * @var string
     * @Assert\NotNull()
     * @Assert\Length(min="10", max="10")
     * @Assert\Regex(pattern="/^9\d{9}/"))
     */
    private $phone;

    /**
     * @var SmsTemplate
     */
    private $smsTemplate;

    /**
     * @var int
     */
    private $utmId;

    /**
     * @return string|null
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @return SmsTemplate|null
     */
    public function getSmsTemplate(): ?SmsTemplate
    {
        return $this->smsTemplate;
    }

    /**
     * @return int|null
     */
    public function getUtmId(): ?int
    {
        return $this->utmId;
    }

    /**
     * @param string|null $phone
     */
    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }

    /**
     * @param SmsTemplate $smsTemplate
     */
    public function setSmsTemplate(SmsTemplate $smsTemplate): void
    {
        $this->smsTemplate = $smsTemplate;
    }

    /**
     * @param int $utmId
     */
    public function setUtmId(int $utmId): void
    {
        $this->utmId = $utmId;
    }
}
