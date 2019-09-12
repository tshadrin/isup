<?php
declare(strict_types=1);

namespace App\ReadModel\Orders\ShowList;


class Order
{
    /** @var integer */
    public $id;
    /** @var string */
    public $full_name;
    /** @var string */
    public $address;
    /** @var string */
    public $ip_address;
    /** @var string */
    public $comment;
    /** @var bool */
    public $is_deleted;
    /** @var integer */
    public $utm_id;
    /** @var string */
    public $mobile_telephone;
    /** @var \DateTime */
    public $created;
    /** @var integer */
    public $completed;
    /** @var integer */
    public $executed;
    /** @var string */
    public $server_name;
    /** @var integer */
    public $user_id;
    /** @var integer */
    public $deleted_id;
    /** @var integer */
    public $status_id;
    /** @var string */
    public $created_user_name;
    /** @var bool  */
    public $emptyPassport = false;

    public function isUtm5Order(): bool
    {
        return !is_null($this->utm_id);
    }
}
