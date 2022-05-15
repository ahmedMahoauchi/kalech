<?php

namespace App\Controller;

use App\Entity\Planning;
use App\Form\PlanningType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/planning")
 */
class PlanningController extends AbstractController
{
    /**
     * @Route("/", name="app_planning_index", methods={"GET"})
     */
    public function index(EntityManagerInterface $entityManager): Response
    {
        $plannings = $entityManager
            ->getRepository(Planning::class)
            ->findAll();

        return $this->render('planning/index.html.twig', [
            'plannings' => $plannings,
        ]);
    }

    /**
     * @Route("/new", name="app_planning_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $planning = new Planning();
        $form = $this->createForm(PlanningType::class, $planning);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($planning);
            $entityManager->flush();
            $MessageBird = new \MessageBird\Client('jnI4Y3TVJZ0FK5W0AlOmsaV9G');
            $Message = new \MessageBird\Objects\Message();
            $Message->originator = '21624331325';
            $Message->recipients = array(+21624331325);
            $Message->body = 'un nouveau planning a été créer';
            //$response = $MessageBird->messages->create($Message);
              // print_r($response);
            return $this->redirectToRoute('app_planning_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('planning/new.html.twig', [
            'planning' => $planning,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{idplanning}", name="app_planning_show", methods={"GET"})
     */
    public function show(Planning $planning): Response
    {
        return $this->render('planning/show.html.twig', [
            'planning' => $planning,
        ]);
    }

    /**
     * @Route("/{idplanning}/edit", name="app_planning_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Planning $planning, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PlanningType::class, $planning);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_planning_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('planning/edit.html.twig', [
            'planning' => $planning,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{idplanning}", name="app_planning_delete", methods={"POST"})
     */
    public function delete(Request $request, Planning $planning, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$planning->getIdplanning(), $request->request->get('_token'))) {
            $entityManager->remove($planning);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_planning_index', [], Response::HTTP_SEE_OTHER);
    }
}
