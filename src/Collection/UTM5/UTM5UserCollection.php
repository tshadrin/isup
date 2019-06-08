<?php
namespace App\Collection\UTM5;

use App\Entity\UTM5\UTM5User;
use Doctrine\Common\Collections\ArrayCollection;


class UTM5UserCollection extends ArrayCollection
{
    static public function createFromData(array $data)
    {
        $users = [];
        foreach($data as $user_data) {
            $users[] = UTM5User::factoryPartial($user_data);
        }
        return new self($users);
    }
}
