<?php

namespace App\Controller;

use App\Entity\Conge;
use App\Form\CongeType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Routing\Annotation\Route;




class CongeController extends AbstractController
{


    /**
     * @Route("/admin/conge", name="display_Conge")
     */
    public function index(): Response
    {

        $conges = $this->getDoctrine()->getManager()->getRepository(Conge::class)->findAll();
        return $this->render('Conge/index.html.twig', [
            'C'=>$conges
        ]);
    }

    /**
     * @Route("/admin", name="display_admin")
     */
    public function indexAdmin(): Response
    {

        return $this->render('Admin/index.html.twig'
        );
    }


    /**
     * @Route("/addConge", name="addConge")
     */
    public function addConge(Request $request): Response
    {
        $conge = new Conge();

        $form = $this->createForm(CongeType::class,$conge);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($conge);//Add
            $em->flush();

            return $this->redirectToRoute('display_Conge');
        }
        return $this->render('conge/createconge.html.twig',['f'=>$form->createView()]);




    }

    /**
     * @Route("/removeConge/{id}", name="supp_conge")
     */
    public function suppressionConge(Conge  $conge): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($conge);
        $em->flush();

        return $this->redirectToRoute('display_Conge');


    }
    /**
     * @Route("/modifConge/{id}", name="modifconge")
     */
    public function modifConge(Request $request,$id): Response
    {
        $conge = $this->getDoctrine()->getManager()->getRepository(conge::class)->find($id);

        $form = $this->createForm(CongeType::class,$conge);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute('display_Conge');
        }
        return $this->render('conge/updateConge.html.twig',['f'=>$form->createView()]);




    }


    /**
     * @Route("/bot", name="botzakazikou")
     */
    public function bot( \Swift_Mailer $mailer): Response
    { $repository = $this->getDoctrine()->getRepository(Conge::class);
        $conges = $repository->findAll();
        $em = $this->getDoctrine()->getManager();

        $compteur=0;


        foreach ($conges as $conges)
        {
            $now = time();
            $date2 = strtotime( $conges->getFinconge()->format('d-m-Y'));
            $diff = abs($date2 - $now); // abs pour avoir la valeur absolute, ainsi éviter d'avoir une différence négative

            try {

                if ($diff < 259200) :
                   $alo=$conges->getTypeconge();


                    $message = (new \Swift_Message('New'))
                        ->setFrom('mkboy423@gmail.com')
                        ->setTo($alo)
                        ->setSubject('[Votre Conge expire dans 3 jours]')
                        ->setBody(
                            $this->renderView(
                                'Emails/sendi.html.twig'),

                            'text/html'
                        );
                    $mailer->send($message);
                    return $this->redirectToRoute('display_Conge');
                endif;
            } catch (Exception $exception){

                echo ($exception);
            }


        }





        return $this->render('conge/index.html.twig',["C"=>$conges]);

    }







}
