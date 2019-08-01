<?php
declare(strict_types=1);

namespace App\Controller\User;

use FOS\UserBundle\Controller\SecurityController as BaseController;
use Symfony\Component\HttpFoundation\{ JsonResponse, Request };
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AjaxController
 * @package MainBundle\Controller\User
 */
class AjaxController extends BaseController
{
    /**
     * @param Request $request
     * @param Session $session
     * @return JsonResponse
     * @Route("/ajax/showhide/", name="ajax_showhide", methods={"GET"}, options={"expose": true})
     */
    public function showHide(Request $request, Session $session): JsonResponse
    {
        if ($request->query->has('block_name') && $request->query->has('value')) {
            $session->set('hide_block_'.$request->query->get('block_name'), $request->query->getBoolean('value'));
            return $this->json(['result' => 'success']);
        } else {
            return $this->json(['result' => 'error']);
        }
    }
}
