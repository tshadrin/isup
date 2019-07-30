<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Phone\Phone;
use App\Form\Phone\DTO\{ Filter, Rows };
use App\Form\Phone\{ PhoneForm, FilterForm, RowsForm};
use App\Repository\Phone\PhoneRepository;
use App\Service\Phone\PagedPhones\{ Command, Handler };
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
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
        $rows = new Rows();
        $rows->value = $rowsOnPage;
        $rowsPerPageForm = $this->createForm(RowsForm::class, $rows);

        $command = new Command(
            $filter,
            $request->query->getInt('page', self::DEFAULT_PAGE),
            $rowsOnPage
        );

        try {
            $pagedPhones = $handler->handle($command);
        } catch (\DomainException $e) {
            $this->addFlash("error", $e->getMessage());
        }

        return $this->render('Phone/phones.html.twig', [
            'filterForm' => $filterForm->createView(),
            'rowsPerPageForm' => $rowsPerPageForm->createView(),
            'phones' => $pagedPhones ?? null,
        ]);
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @Route("/add", name=".add", methods={"GET", "POST"})
     */
    public function add(Request $request, PhoneRepository $phoneRepository): Response
    {
        $phone = $phoneRepository->getNew();
        $form = $this->createForm(PhoneForm::class, $phone);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $phoneRepository->save($form->getData());
            $phoneRepository->flush();
            $this->addFlash('notice', 'Phone added.');
            return $this->redirectToRoute('phone');
        } else {
            return $this->render('Phone/form.html.twig', ['form' => $form->createView(),]);
        }
    }

    /**
     * @return RedirectResponse|Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @Route("/{phone_id}/edit/", name=".edit", methods={"GET", "POST"}, requirements={"phone_id": "\d+"})
     * @ParamConverter("phone", options={"id" = "phone_id"})
     */
    public function edit(Phone $phone, Request $request, PhoneRepository $phoneRepository)
    {
        try {
            $form = $this->createForm(PhoneForm::class, $phone);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $phoneRepository->save($form->getData());
                $phoneRepository->flush();
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
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @Route("/{phone_id}/delete", name=".delete", methods={"GET", "POST"}, requirements={"phone_id": "\d+"})
     * @ParamConverter("phone", options={"id" = "phone_id"})
     * @IsGranted("ROLE_MODERATOR")
     */
    public function delete(Phone $phone, Request $request, PhoneRepository $phoneRepository): RedirectResponse
    {
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            return $this->redirectToRoute('phone');
        }

        try{
            $phoneRepository->delete($phone);
            $phoneRepository->flush();
            $this->addFlash('notice', 'phone.deleted');
        } catch (\DomainException $e) {
            $this->addFlash('error', $e->getMessage());
        }
        return $this->redirectToRoute('phone');
    }

    /**
     * @Route("", name=".rows", methods={"POST"})
     */
    public function rowsPerPage(Request $request, Session $session): RedirectResponse
    {
        $rows = new Rows();
        $rowsPerPageForm = $this->createForm(RowsForm::class, $rows);
        $rowsPerPageForm->handleRequest($request);
        if ($rowsPerPageForm->isSubmitted() && $rowsPerPageForm->isValid()) {
            $session->set('rowsOnPage', $rows->value);
        } else {
            $this->addFlash("error", "Incorrect filter value");
        }
        return $this->redirect($request->getUri());
    }
}
