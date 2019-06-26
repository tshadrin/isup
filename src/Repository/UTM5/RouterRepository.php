<?php



namespace App\Repository\UTM5;

use App\Collection\UTM5\RouterCollection;
use App\Mapper\UTM5\RouterMapper;

class RouterRepository
{

    /**
     * @var RouterMapper
     */
    private $routerMapper;

    public function __construct(RouterMapper $routerMapper)
    {
        $this->routerMapper = $routerMapper;
    }

    public function findByUserId(int $user_id): ?RouterCollection
    {
        return $this->routerMapper->getRouters($user_id);
    }
}