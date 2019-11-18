<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Vlan\Vlan;
use App\Form\Vlan\{DTO\Filter, FilterForm, VlanForm};
use App\Repository\Vlan\VlanRepository;
use App\Service\Vlan\PagedVlans\{ Command, Handler };
use PAMI\Message\Action\QueueMemberRingInUse;
use PAMI\Message\Action\QueuesAction;
use PAMI\Message\Action\QueueStatusAction;
use PAMI\Message\Action\QueueSummaryAction;
use PAMI\Message\Event\DialBeginEvent;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{ Request, Response, RedirectResponse, Session\Session};
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/vlan", name="vlan")
 */
class VlanController extends AbstractController
{
    const DEFAULT_ROWS_ON_PAGE = 20;
    const DEFAULT_PAGE = 1;

    /**
     * @return Response
     * @Route("", name="", methods={"GET"})
     * @IsGranted("ROLE_SUPPORT")
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
            'vlans' => $pagedVlans ?? null,
        ]);
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @Route("/add", name=".add", methods={"GET", "POST"})
     * @IsGranted("ROLE_SUPPORT")
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
     * @IsGranted("ROLE_SUPPORT")
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

        try {
            $vlanRepository->delete($vlan);
            $vlanRepository->flush();
            $this->addFlash('notice', 'vlan.deleted');
        } catch (\DomainException $e) {
            $this->addFlash('error', $e->getMessage());
        }
        return $this->redirectToRoute('vlan');
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @Route("/get/{number}", name=".get", methods={"GET","POST"}, requirements={"number": "\d+"})
     * @Entity("vlan",  expr="repository.findOneByNumber(number)")
     * @IsGranted("IS_AUTHENTICATED_ANONYMOUSLY")
     */
    public function getVlan(Vlan $vlan, Request $request, VlanRepository $vlanRepository): Response
    {
        return $this->json(['number' => $vlan->getNumber(), 'name' => $vlan->getName(), 'points' => $vlan->getPoints()]);
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @Route("/get/all", name=".get.all", methods={"GET","POST"})
     * @IsGranted("IS_AUTHENTICATED_ANONYMOUSLY")
     */
    public function getVlans(Request $request, VlanRepository $vlanRepository): Response
    {
        $v = [];
        /** @var Vlan[] $vlans */
        $vlans = $vlanRepository->findAllNotDeleted();
        foreach ($vlans as $vlan) {
            $v[] = ['number' => $vlan->getNumber(), 'name' => $vlan->getName(), 'points' => $vlan->getPoints()];
        }
        return $this->json($v);
    }

    /**
     * @Route("/test", name=".get.all", methods={"GET","POST"})
     * @IsGranted("IS_AUTHENTICATED_ANONYMOUSLY")
     */
    public function test(): Response
    {
        $options = array(
            'host' => '10.3.7.43',
            'scheme' => 'tcp://',
            'port' => 5038,
            'username' => 'monast',
            'secret' => 'ddcc93fa8e2959241f4ba9e7bd0022b1',
            'connect_timeout' => 10,
            'read_timeout' => 10
        );
        $client = new \PAMI\Client\Impl\ClientImpl($options);
        $client->open();
        dump($response = $client->send(new QueueStatusAction('586')));
        $client->close();
        exit;
        //$client->registerEventListener(function ($event) {
        //    if($event instanceof DialBeginEvent) {
        //        dump($event);
        //        exit;
        //    }
        //});
       // while(true) {
      //      $client->process();
       //     usleep(1000);
      //  }
    }
}
