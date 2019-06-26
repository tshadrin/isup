<?php

namespace App\Repository\UTM5;

use App\Collection\UTM5\GroupCollection;
use App\Mapper\UTM5\GroupMapper;

class GroupRepository
{

    /**
     * @var GroupMapper
     */
    private $groupMapper;

    public function __construct(GroupMapper $groupMapper)
    {
        $this->groupMapper = $groupMapper;
    }

    public function findByUserId(int $user_id): ?GroupCollection
    {
        return $this->groupMapper->getGroups($user_id);
    }
}