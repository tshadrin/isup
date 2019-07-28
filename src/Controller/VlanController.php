<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Vlan\Vlan;
use App\Form\Vlan\{DTO\Filter, FilterForm, VlanForm};
use App\Repository\Vlan\VlanRepository;
use App\Service\Vlan\PagedVlans\{ Command, Handler };
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{ Request, Response, RedirectResponse, Session\Session};
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class VlanController
 * @package Mainbundle\Controller\Vlan
 * @IsGranted("ROLE_SUPPORT")
 * @Route("/vlan", name="vlan")
 */
class VlanController extends AbstractController
{
    const DEFAULT_ROWS_ON_PAGE = 20;
    const DEFAULT_PAGE = 1;

    /**
     * @return Response
     * @Route("", name="", methods={"GET"})
     */
    public function index(Request $request, Session $session, Handler $handler): Response
    {
        $filter = new Filter();

        $filterForm = $this->createForm(FilterForm::class, $filter);
        $filterForm->handleRequest($request);

        $rowsOnPage = $session->get('rowsOnPage', self::DEFAULT_ROWS_ON_PAGE);

        $command = new Command(
            $filter,
            $request->query->getInt('page', self::DEFAULT_PAGE),
            $rowsOnPage
        );

        try {
            $pagedVlans = $handler->handle($command);
        } catch (\DomainException $e) {
            $this->addFlash("error", $e->getMessage());
        }

        return $this->render('Vlan/vlans.html.twig', [
            'filterForm' => $filterForm->createView(),
            'vlans' => isset($pagedVlans)?$pagedVlans:null,
        ]);
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @Route("/add", name=".add", methods={"GET", "POST"})
     */
    public function add(Request $request, VlanRepository $vlanRepository): Response
    {
        $vlan = $vlanRepository->getNew();
        $form = $this->createForm(VlanForm::class, $vlan);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $vlanRepository->save($form->getData());
            $vlanRepository->flush();
            $this->addFlash('notice', 'Vlan added.');
            return $this->redirectToRoute('vlan');
        } else {
            return $this->render('Vlan/form.html.twig', ['form' => $form->createView(),]);
        }
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @Route("/{vlan_id}/edit", name=".edit", methods={"GET", "POST"}, requirements={"vlan_id": "\d+"})
     * @ParamConverter("vlan", options={"id" = "vlan_id"})
     */
    public function edit(Vlan $vlan, Request $request, VlanRepository $vlanRepository): Response
    {
        try {
            $form = $this->createForm(VlanForm::class, $vlan);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $vlanRepository->save($form->getData());
                $vlanRepository->flush();
                $this->addFlash('notice', 'Vlan changes are saved.');
            } else {
                return $this->render('Vlan/form.html.twig', ['form' => $form->createView(), 'edit' => true]);
            }
        } catch (\DomainException $e) {
            $this->addFlash("error", $e->getMessage());
        }
        return $this->redirectToRoute("vlan");
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @Route("/{vlan_id}/delete", name=".delete", methods={"GET", "POST"}, requirements={"vlan_id": "\d+"})
     * @ParamConverter("vlan", options={"id" = "vlan_id"})
     * @IsGranted("ROLE_MODERATOR")
     */
    public function delete(Vlan $vlan, Request $request, VlanRepository $vlanRepository): RedirectResponse
    {
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            return $this->redirectToRoute('vlan');
        }

        try{
            $vlanRepository->delete($vlan);
            $vlanRepository->flush();
            $this->addFlash('notice', 'vlan.deleted');
        } catch (\DomainException $e) {
            $this->addFlash('error', $e->getMessage());
        }
        return $this->redirectToRoute('vlan');
    }
}
