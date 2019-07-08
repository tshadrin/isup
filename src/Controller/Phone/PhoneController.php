<?php
declare(strict_types=1);

namespace App\Controller\Phone;

use App\Form\Phone\{ PhoneForm, PhoneFilterForm, RowsForm };
use App\Repository\Phone\PhoneRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{ Request, Response, RedirectResponse };
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PhoneController
 * @package MainBundle\Controller\Phone
 */
class PhoneController extends AbstractController
{
    /**
     * Вывод списка телефонов постранично
     * @param string $filter
     * @param Request $request
     * @param Session $session
     * @param PhoneRepository $phone_repository
     * @param PaginatorInterface $paginator
     * @return RedirectResponse|Response
     * @Route("/phone/{filter}/list/", name="phone_default", defaults={"filter": "_all"}, methods={"GET"})
     */
    public function getAll(
        string $filter,
        Request $request,
        Session $session,
        PhoneRepository $phone_repository,
        PaginatorInterface $paginator
    )
    {
        $phone_filter_form = $this->createForm(PhoneFilterForm::class);

        $rows_form = $this->createForm(RowsForm::class);
        $rows = $session->get('rows', 20);

        $rows_form->setData(['rows' => $rows]);

        $page = $request->query->getInt('page', 1);
        try {
            if ("_all" === $filter) {
                $phones = $phone_repository->getAll();
            } else {
                $phone_filter_form->handleRequest($request);
                $phone_filter_form->setData(['search' => $filter]);
                $phones = $phone_repository->getFromAllFields($filter);
            }
        } catch (\DomainException $e) {
            $this->addFlash("error", $e->getMessage());
            return $this->redirectToRoute("phone_default");
        }

        $paged_phones =  $paginator->paginate($phones, $page, $rows);
        $paged_phones->setCustomParameters(['align' => 'center', 'size' => 'small',]);

        return $this->render('Phone/phones.html.twig', [
            'phone_filter_form' => $phone_filter_form->createView(),
            'form' => $rows_form->createView(),
            'rows_on_page' => $rows,
            'phones' => $paged_phones,
        ]);
    }

    /**
     * Редактирование телефона
     * @param int $id
     * @param Request $request
     * @param PhoneRepository $phone_repository
     * @return RedirectResponse|Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @Route("/phone/{id}/edit/", name="phone_edit", methods={"GET", "POST"}, requirements={"id": "\d+"})
     */
    public function edit(int $id, Request $request, PhoneRepository $phone_repository)
    {
        try {
            $phone = $phone_repository->getById($id);
            $form = $this->createForm(PhoneForm::class, $phone);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $phone_repository->save($form->getData());
                $phone_repository->flush();
                $this->addFlash('notice', 'Phone changes are saved.');
            } else {
                return $this->render('Phone/form.html.twig', ['form' => $form->createView(), 'edit' => true]);
            }
        } catch (\DomainException $e) {
            $this->addFlash("error", $e->getMessage());
        }
        return $this->redirectToRoute("phone_default");
    }

    /**
     * Добавление телефона
     * @param Request $request
     * @param PhoneRepository $phone_repository
     * @return RedirectResponse|Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @Route("/phone/add/", name="phone_add", methods={"GET", "POST"})
     */
    public function add(Request $request, PhoneRepository $phone_repository)
    {
        $phone = $phone_repository->getNew();
        $form = $this->createForm(PhoneForm::class, $phone);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $phone_repository->save($form->getData());
            $phone_repository->flush();
            $this->addFlash('notice', 'Phone added.');
            return $this->redirectToRoute('phone_default');
        } else {
            return $this->render('Phone/form.html.twig', ['form' => $form->createView(),]);
        }
    }

    /**
     * Удаление телефона
     * @param int $id
     * @param PhoneRepository $phone_repository
     * @return RedirectResponse
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @Route("/phone/{id}/delete", name="phone_delete", methods={"GET", "POST"}, requirements={"id": "\d+"})
     */
    public function delete(int $id, PhoneRepository $phone_repository): RedirectResponse
    {
        try{
            $phone = $phone_repository->getById($id);
            $phone_repository->delete($phone);
            $phone_repository->flush();
            $this->addFlash('notice', 'phone.deleted');
        } catch (\DomainException $e) {
            $this->addFlash('error', $e->getMessage());
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }
        return $this->redirectToRoute('phone_default');
    }

    /**
     * Обработка формы поиска телефонов
     * @param Request $request
     * @return RedirectResponse
     * @Route("/phone/find/", name="phone_find_process", methods={"POST"})
     */
    public function findProcess(Request $request): RedirectResponse
    {
        $form = $this->createForm(PhoneFilterForm::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            return $this->redirectToRoute('phone_default', ['filter' => $data['search']]);
        }
        return $this->redirectToRoute('phone_default');
    }

    /**
     * Изменение количества выводимых записей на странице
     * @param Request $request
     * @param Session $session
     * @return RedirectResponse
     * @Route("/phone/{filter}/list/", name="phone_rows", defaults={"filter": "_all"}, methods={"POST"})
     */
    public function setRowsOnPage(Request $request, Session $session)
    {
        $form = $this->createForm(RowsForm::class);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $data = $form->getData();
            $session->set('rows', $data['rows']);
        }
        return $this->redirectToRoute('phone_default');
    }
}
