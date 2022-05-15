<?php

namespace App\Controller;

use App\Repository\CategorieProduitRepository;
use App\Repository\ProduitShopRepository;
use App\service\cart\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShopHomeController extends AbstractController
{
    /**
     * @Route("/a", name="app_shop_home")
     */
    public function index(ProduitShopRepository $rep,CategorieProduitRepository $cat ,CartService $cartService): Response
    {

        $produits = $rep->findAll();
        $categories = $cat->findAll();
        return $this->render('shop_home/index.html.twig', [
            'controller_name' => 'ShopHomeController',
             'produits' => $produits,
             'categories' => $categories,
             'countProduct' => $cartService->getFullCart()
        ]);
    }
}
