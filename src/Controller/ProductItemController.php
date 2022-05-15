<?php

namespace App\Controller;

use App\Repository\ProduitShopRepository;
use App\service\cart\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductItemController extends AbstractController
{
    /**
     * @Route("/product/item/{id}", name="product_item")
     */
    public function index($id,ProduitShopRepository $rep,CartService $cartService): Response
    {
        $produit = $rep->find($id);

        $relatedProducts = $rep->find4RelatedProducts($produit->getCategorie()->getId());

        

        return $this->render('product_item/index.html.twig', [
            'controller_name' => 'ProductItemController',
            'produit' => $produit,
            'related' => $relatedProducts,
            'numpan'=> sizeof($cartService->getFullCart())
        ]);
    }
}
