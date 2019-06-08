<?php

namespace App\Controller\User;

use FOS\UserBundle\Controller\SecurityController as BaseController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class SecurityController extends BaseController
{
    /**
     * @param array $data
     * @return RedirectResponse|Response
     */
    protected function renderLogin(array $data)
    {
        if ($this->isGranted('ROLE_USER'))
            return $this->redirectToRoute('fos_user_profile_show');

      //  return $this->render('FOSUserBundle/Security/login.html.twig', $data);
    }
}
