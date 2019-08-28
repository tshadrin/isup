<?php
declare(strict_types=1);

namespace App\ReadModel\Orders\ShowList\Filter;

use App\Entity\Intercom\Status;

use App\Service\SMS\smscSender;
use Symfony\Component\Validator\Constraints as Assert;

class Filter
{
    public const PRESET_ISTRA = 'istra';
    public const PRESET_DEDOVSK = 'dedovsk';
    public const PRESET_ACTUAL = 'actual';
    public const PRESET_OUTDATE = 'outdate';
    public const PRESET_CURRENT_USER = 'current_user';
    public const PRESET_NOT_ASSIGNED = 'not_assigned';

    /** @var string */
    public $text;
    /**
     * @var string
     * @Assert\Choice({Filter::PRESET_ISTRA, Filter::PRESET_DEDOVSK, Filter::PRESET_ACTUAL, Filter::PRESET_OUTDATE, Filter::PRESET_CURRENT_USER, Filter::PRESET_NOT_ASSIGNED})
     */
    public $preset;
    /** @var Status */
    public $status;
    /** @var array */
    public $interval;
}