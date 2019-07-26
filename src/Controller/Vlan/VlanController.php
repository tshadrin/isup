<?php
declare(strict_types=1);

namespace App\Controller\Vlan;

use App\Form\Vlan\{DTO\Filter, FilterForm, VlanForm};
use App\Repository\Vlan\VlanRepository;
use App\Service\Vlan\PagedVlans\Command;
use App\Service\Vlan\PagedVlans\Handler;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
     * @param Request $request
     * @param Session $session
     * @param PaginatorInterface $paginator
     * @param VlanRepository $vlan_repository
     * @return RedirectResponse|Response
     * @Route("", name="", methods={"GET"})
     */
    public function index(Request $request, Session $session, Handler $handler)
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
     * Редактирование vlan
     * @param int $id
     * @param Request $request
     * @param VlanRepository $vlan_repository
     * @return RedirectResponse|Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @Route("/{id}/edit", name=".edit", methods={"GET", "POST"}, requirements={"id": "\d+"})
     */
    public function edit(int $id, Request $request, VlanRepository $vlan_repository)
    {
        try {
            $vlan = $vlan_repository->findById($id);
            $form = $this->createForm(VlanForm::class, $vlan);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $vlan_repository->save($form->getData());
                $vlan_repository->flush();
                $this->addFlash('notice', 'Vlan changes are saved.');
            } else {
                return $this->render('Vlan/form.html.twig', ['form' => $form->createView(), 'edit' => true]);
            }
        } catch (\DomainException $e) {
            $this->addFlash("error", $e->getMessage());
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }
        return $this->redirectToRoute("vlan");
    }

    /**
     * Добавление телефона
     * @param Request $request
     * @param VlanRepository $vlan_repository
     * @return RedirectResponse|Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @Route("/add", name=".add", methods={"GET", "POST"})
     */
    public function add(Request $request, VlanRepository $vlan_repository)
    {
        $vlan = $vlan_repository->getNew();
        $form = $this->createForm(VlanForm::class, $vlan);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $vlan_repository->save($form->getData());
            $vlan_repository->flush();
            $this->addFlash('notice', 'Vlan added.');
            return $this->redirectToRoute('vlan');
        } else {
            return $this->render('Vlan/form.html.twig', ['form' => $form->createView(),]);
        }
    }

    /**
     * Метод для удаления влана
     * @param $id
     * @param VlanRepository $vlan_repository
     * @return RedirectResponse
     * @Route("/{id}/delete", name=".delete", methods={"GET", "POST"}, requirements={"id": "\d+"})
     */
    public function delete(int $id, VlanRepository $vlan_repository): RedirectResponse
    {
        try{
            $vlan = $vlan_repository->findById($id);
            $vlan_repository->delete($vlan);
            $vlan_repository->flush();
            $this->addFlash('notice', 'vlan.deleted');
        } catch (\DomainException $e) {
            $this->addFlash('error', $e->getMessage());
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }
        return $this->redirectToRoute('vlan');
    }
}
