<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Form\CommandeType;
use App\service\cart\CartService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CheckoutController extends AbstractController
{
    /**
     * @Route("/checkout", name="checkout")
     */
    public function index(CartService $cartService,Request $request,ManagerRegistry $managerRegistry): Response
    {

        $commande = new Commande();
        $form = $this->createForm(CommandeType::class, $commande);


        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $commande->setPrix($cartService->getTotal());

            $manager = $managerRegistry->getManager();
            $manager->persist($commande);
            $manager->flush();

            $cartService->resetPanier();
            
            

            return $this->redirectToRoute('app_shop_home');
        }


        $Panier = $cartService->getFullCart();
        

        return $this->render('checkout/index.html.twig', [
            'controller_name' => 'CheckoutController',
            'items' => $Panier,
            'total' => $cartService->getTotal(),
            'form' =>$form->createView()
        ]);
    }
}
