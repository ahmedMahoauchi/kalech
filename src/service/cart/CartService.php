<?php

 namespace App\service\cart;

use App\Repository\CategorieProduitRepository;
use App\Repository\ProduitShopRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

 class CartService{

    protected $session,$prodRep,$catRep;

    public function __construct( SessionInterface $session,ProduitShopRepository  $prodRep,CategorieProduitRepository $catRep) {
        
        $this->session = $session;
        $this->prodRep = $prodRep;
        $this->catRep = $catRep;


    }

     public function resetPanier()
    {
       

        $this->session->set('panier',[]);
    }


    public function add($id) {
        $panier = $this->session->get('panier',[]);

       

        if (!empty($panier[$id])) {
            $panier[$id]++;
        } else {
            $panier[$id] = 1;
        }
        

        $this->session->set('panier',$panier);
        
    }

    public function remove($id) {
        $panier = $this->session->get('panier',[]);


        if (!empty($panier[$id])) {
            unset($panier[$id]);
        }

        $this->session->set('panier',$panier);
    }

    public function getFullCart() : array {
        $panier = $this->session->get('panier',[]);

        $panierWithData = [];
       
        foreach ($panier as $id => $quantity) {

           

            $product = $this->prodRep->find($id);
            if (is_null($product)) {
                $categorie= null;
            }
            else {
                $categorie =  $this->catRep->find($this->prodRep->find($id)->getCategorie());
            }
            $panierWithData[]=[
                'product' => $product ,
                'categorie' => $categorie,
                'quantite' => $quantity

               
            ];
        }

        dump($panierWithData);

        return $panierWithData;
    }

    public function getTotal() : float {
        $total = 0;
        foreach ($this->getFullCart() as $item) {
            if (!is_null($item['product'])) {
            $total += $item['product']->getPrix() * $item['quantite'];
            }
         }
         return $total;
    }
 }
