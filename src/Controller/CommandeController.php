<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Repository\CommandeRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommandeController extends AbstractController
{
    /**
     * @Route("/commande", name="app_commande")
     */
    public function index(CommandeRepository $rep): Response
    {

        $commandes = $rep->findAll();



        return $this->render('commande/index.html.twig', [
            'commandes' => $commandes,

        ]);
    }

  /**
     * @Route("/commande/supprimer/{id}",name="supprimer_commande")
     */
    public function supprimerCommande($id,CommandeRepository $rep, ManagerRegistry  $managerRegistry){
        $commande = $rep->find($id);
        

    if ($commande) {

        $manager=$managerRegistry->getManager();
        $manager->remove($commande);
        $manager->flush();

        $this->addFlash(
            'info',
            'Commande supprimé avec succées'

        );
       
    }
       
    return $this->redirectToRoute('app_commande');

    }
}
