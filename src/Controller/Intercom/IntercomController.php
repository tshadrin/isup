<?php
declare(strict_types=1);

namespace App\Controller\Intercom;

use App\Form\Intercom\TaskForm;
use App\Service\Intercom\IntercomService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{ Request, RedirectResponse, Response};
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class IntercomController
 * @package App\Controller\Intercom
 * @IsGranted("ROLE_INTERCOMS")
 */
class IntercomController extends AbstractController
{
    /**
     * Постраничный список всех задач
     * @param Request $request
     * @param IntercomService $intercomService
     * @return Response
     * @Route("/intercom/", name="intercom_index", methods={"GET"})
     */
    public function index(Request $request, IntercomService $intercomService): Response
    {
        try {
            $tasks = $intercomService->getAllTasksPaginate($request->query->getInt('page', 1));
        } catch (\DomainException $e) {
            $this->addFlash('error', $e->getMessage());
            return $this->render('Intercom/index.html.twig');
        }
        return $this->render('Intercom/index.html.twig', ['tasks' => $tasks]);
    }

    /**
     * Удаление задачи
     * @param int $id
     * @param IntercomService $intercomService
     * @return RedirectResponse
     * @Route("/intercom/{id}/delete/", name="intercom_delete", methods={"GET"}, requirements={"id": "\d+"})
     */
    public function delete(int $id, IntercomService $intercomService): RedirectResponse
    {
        try {
            $intercomService->deleteTask($id);
            $this->addFlash('notice','Task deleted');
        } catch (\DomainException $e) {
            $this->addFlash('error', $e->getMessage());
        }
        return $this->redirectToRoute('intercom_index');
    }

    /**
     * Редактирование заявки
     * @param int $id
     * @param Request $request
     * @param IntercomService $intercomService
     * @return RedirectResponse|Response
     * @Route("/intercom/{id}/edit/", name="intercom_edit", methods={"GET","POST"}, requirements={"id": "\d+"})
     */
    public function edit(int $id, Request $request, IntercomService $intercomService)
    {
        try {
            $task = $intercomService->getOneTaskById($id);
            $form = $this->createForm(TaskForm::class, $task);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $intercomService->saveTask($form->getData());
                $this->addFlash('notice', 'task.edited');
                if($form['saveandlist']->isCLicked()) {
                    return $this->redirectToRoute('intercom_index');
                }
            }
            return $this->render('Intercom/task_form.html.twig', ['form' => $form->createView(), 'edit' => true]);
        } catch (\DomainException $e) {
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('intercom_index');
        }
    }

    /**
     * Создание новой задачи
     * @param Request $request
     * @param IntercomService $intercomService
     * @return RedirectResponse|Response
     * @throws \Exception
     * @Route("/intercom/add/", name="intercom_add", methods={"GET", "POST"})
     */
    public function add(Request $request, IntercomService $intercomService)
    {
        $task = $intercomService->getNewTask();
        $form = $this->createForm(TaskForm::class, $task);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $intercomService->saveTask($form->getData());
            $this->addFlash('notice', 'task.created');
            if($form['saveandlist']->isCLicked())
                return $this->redirectToRoute('intercom_index');
        }
        return $this->render('Intercom/task_form.html.twig', ['form' => $form->createView(),]);
    }
}
