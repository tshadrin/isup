<?php
declare(strict_types=1);

namespace App\Controller\Phone;

use App\Form\Phone\{Filter, PhoneForm, PhoneFilterForm, RowsForm};
use App\Repository\Phone\PhoneRepository;
use App\Service\Phone\PagedPhones\Command;
use App\Service\Phone\PagedPhones\Handler;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{ Request, Response, RedirectResponse };
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_SUPPORT")
 * @Route("/phone", name="phone")
 */
class PhoneController extends AbstractController
{
    const DEFAULT_ROWS_PER_PAGE = 20;
    const DEFAULT_PAGE = 1;

    /**
     * @return RedirectResponse|Response
     * @Route("", name="", methods={"GET"})
     */
    public function index(Request $request, Session $session, Handler $handler)
    {

        $filter = new Filter();
        $phoneFilterForm = $this->createForm(PhoneFilterForm::class, $filter);
        $phoneFilterForm->handleRequest($request);

        $rowsPerPageForm = $this->createForm(RowsForm::class);
        $rowsPerPage = $session->get('rows', self::DEFAULT_ROWS_PER_PAGE);
        $rowsPerPageForm->setData(['rows' => $rowsPerPage]);

        $command = new Command(
            $filter,
            $request->query->getInt('page', self::DEFAULT_PAGE),
            $rowsPerPage
        );

        try {
            $pagedPhones = $handler->handle($command);
        } catch (\DomainException $e) {
            $this->addFlash("error", $e->getMessage());
            return $this->redirectToRoute("phone");
        }

        return $this->render('Phone/phones.html.twig', [
            'phoneFilterForm' => $phoneFilterForm->createView(),
            'rowsPerPageForm' => $rowsPerPageForm->createView(),
            'phones' => $pagedPhones,
        ]);
    }

    /**
     * @param Request $request
     * @param PhoneRepository $phone_repository
     * @return RedirectResponse|Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @Route("/add/", name=".add", methods={"GET", "POST"})
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
            return $this->redirectToRoute('phone');
        } else {
            return $this->render('Phone/form.html.twig', ['form' => $form->createView(),]);
        }
    }

    /**
     * @param int $id
     * @param Request $request
     * @param PhoneRepository $phone_repository
     * @return RedirectResponse|Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @Route("/{id}/edit/", name=".edit", methods={"GET", "POST"}, requirements={"id": "\d+"})
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
        return $this->redirectToRoute("phone");
    }

    /**
     * @param int $id
     * @param PhoneRepository $phone_repository
     * @return RedirectResponse
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @Route("/{id}/delete", name=".delete", methods={"GET", "POST"}, requirements={"id": "\d+"})
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
        }
        return $this->redirectToRoute('phone');
    }

    /**
     * Обработка формы поиска телефонов
     * @param Request $request
     * @return RedirectResponse
     * @Route("/find/", name=".find_process", methods={"POST"})
     */
    public function findProcess(Request $request): RedirectResponse
    {
        $form = $this->createForm(PhoneFilterForm::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            return $this->redirectToRoute('phone', ['filter' => $data['search']]);
        }
        return $this->redirectToRoute('phone');
    }

    /**
     * @Route("", name=".rows", methods={"POST"})
     */
    public function rowsPerPage(Request $request, Session $session): RedirectResponse
    {
        $rowsPerPageForm = $this->createForm(RowsForm::class);
        $rowsPerPageForm->handleRequest($request);
        if ($rowsPerPageForm->isSubmitted()) {
            $data = $rowsPerPageForm->getData();
            $session->set('rows', $data['rows']);
        }
        return $this->redirect($request->getUri());
    }
}
