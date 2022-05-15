<?php

namespace App\Controller;

use App\service\cart\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{

    protected $cartService;

    public function __construct(CartService $cartService) {
        $this->cartService = $cartService;
    }

    /**
     * @Route("/cart", name="cart")
     */
    public function index(): Response
    {
        return $this->render('cart/index.html.twig', [
            'items' => $this->cartService->getFullCart(),
            'total' => $this->cartService->getTotal()
        ]);
    }

    /**
     * @Route("/cart/add/{id}", name="cart_add")
     */
    public function add($id){

       
        $this->cartService->add($id);
        return $this->redirectToRoute('cart');
    }

    /**
     * @Route("cart/remove/{id}", name="cart_remove")
     */
    public function remove($id){

        $this->cartService->remove($id);
        return $this->redirectToRoute('cart');
    }
}
