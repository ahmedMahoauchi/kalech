<?php

namespace App\Controller;

use App\Repository\CategorieProduitRepository;
use App\Repository\ProduitShopRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncode;

class AdminDashboardController extends AbstractController
{
    /**
     * @Route("/admin", name="app_admin_dashboard")
     */
    public function index(CategorieProduitRepository $repo,ProduitShopRepository $repop): Response
    {

       

        return $this->render('admin_dashboard/index.html.twig', [
            'controller_name' => 'AdminDashboardController',
        ]);
    }


    /**
     * @Route("stat",name="stat")
     */
    public function stat(CategorieProduitRepository $repCat,ProduitShopRepository $repProd){
       
       

       $categories = $repCat->findAll();

       $catNom =[];
       $prodCount = [];
       $catColor= [];

       foreach ($categories as $cat) {
           $catNom[] = $cat->getNomCategorie();
           $prodCount[] =count($repProd->find4RelatedProducts($cat->getId())) ;
           $catColor[] = '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
       }

        return $this->render("admin_dashboard/stat.html.twig",[
            'catNom' => json_encode($catNom),
            'prodCount' => json_encode($prodCount),
            'catColor' => json_encode($catColor)

        ]);
    }
}
