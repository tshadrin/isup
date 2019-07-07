<?php
declare(strict_types=1);

namespace App\Controller\Vlan;

use App\Form\Vlan\{ VlanFilterForm, VlanForm };
use App\Repository\Vlan\VlanRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{ Request, Response, RedirectResponse};
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Class VlanController
 * @package Mainbundle\Controller\Vlan
 */
class VlanController extends AbstractController
{
    /**
     * Вывод списка vlan постранично
     * @param $filter
     * @param Request $request
     * @param Session $session
     * @param PaginatorInterface $paginator
     * @param VlanRepository $vlan_repository
     * @return RedirectResponse|Response
     * @Route("/vlan/{filter}/list/", name="vlan_default", defaults={"filter": "_all"}, methods={"GET"})
     */
    public function getAll(
        string $filter,
        Request $request,
        Session $session,
        PaginatorInterface $paginator,
        VlanRepository $vlan_repository)
    {
        $vlan_filter_form = $this->createForm(VlanFilterForm::class);

        $rows = $session->get('rows', 20);

        $page = $request->query->getInt('page', 1);

        try {
            if ("_all" === $filter) {
                $vlans = $vlan_repository->findAll();
            } else {
                $vlan_filter_form->handleRequest($request);
                $vlan_filter_form->setData(['search' => $filter]);
                $vlans = $vlan_repository->findByCriteria($filter);
            }
        } catch (\DomainException $e) {
            $this->addFlash("error", $e->getMessage());
            return $this->redirectToRoute("vlan_default");
        }  catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        $paged_vlans =  $paginator->paginate($vlans, $page, $rows);
        $paged_vlans->setCustomParameters(['align' => 'center', 'size' => 'small',]);

        return $this->render('Vlan/vlans.html.twig', [
            'vlans' => $paged_vlans,
            'rows_on_page' => $rows,
            'vlan_filter_form' => $vlan_filter_form->createView(),
        ]);
    }

    /**
     * Редактирование vlan
     * @param $id
     * @param Request $request
     * @param VlanRepository $vlan_repository
     * @return RedirectResponse|Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @Route("/vlan/{id}/edit/", name="vlan_edit", methods={"GET", "POST"}, requirements={"id": "\d+"})
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
        return $this->redirectToRoute("vlan_default");
    }

    /**
     * Добавление телефона
     * @param Request $request
     * @param VlanRepository $vlan_repository
     * @return RedirectResponse|Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @Route("/vlan/add/", name="vlan_add", methods={"GET", "POST"})
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
            return $this->redirectToRoute('vlan_default');
        } else {
            return $this->render('Vlan/form.html.twig', ['form' => $form->createView(),]);
        }
    }

    /**
     * Метод для удаления влана
     * @param $id
     * @param VlanRepository $vlan_repository
     * @return RedirectResponse
     * @Route("/vlan/{id}/delete", name="vlan_delete", methods={"GET", "POST"}, requirements={"id": "\d+"})
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
        return $this->redirectToRoute('vlan_default');
    }

    /**
     * Обработка формы поиска влана
     * @param Request $request
     * @return RedirectResponse
     * @Route("/vlan/find/", name="vlan_find_process", methods={"POST"})
     */
    public function findProcess(Request $request): RedirectResponse
    {
        $form = $this->createForm(VlanFilterForm::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            return $this->redirectToRoute('vlan_default', ['filter' => $data['search']]);
        }
        return $this->redirectToRoute('vlan_default');
    }
}
