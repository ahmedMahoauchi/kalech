<?php

namespace App\Controller;

use App\Entity\ProduitShop;
use App\Form\AddProduitType;
use App\Repository\CategorieProduitRepository;
use App\Repository\ProduitShopRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Dompdf\Dompdf;
use Dompdf\Options;

class ProduitAdminController extends AbstractController
{
    /**
     * @Route("/produit/admin", name="app_produit_admin")
     */
    public function index(Request $request,CategorieProduitRepository $repCat,ProduitShopRepository $rep,ManagerRegistry  $managerRegistry): Response
    {

        

        $produit = new ProduitShop();
        
        
        $form = $this->createForm(AddProduitType::class,$produit);


        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('image')->getData();
            $fileName = md5(uniqid()).'.'.$file->guessExtension();

            try{
                $file->move(
                    $this->getParameter('uploads_directory'),
                    $fileName
                );
            }catch(FileException $e){

            }


            $produit->setImage($fileName);

            $manager = $managerRegistry->getManager();
            $manager->persist($produit);
            $manager->flush();

            $this->addFlash(
                'info',
                'Produit ajouté avec succés !!!'

            );
        }

        $produits = $rep->findAll();
        return $this->render('produit_admin/index.html.twig', [
            'produits' => $produits,
            'form' =>$form->createView(),
        ]);
    }

        /**
     * @Route("/produit/admin/supprimer/{id}",name="Supprimer_Produit")
     */
    public function supprimerProduit($id,ProduitShopRepository $rep, ManagerRegistry  $managerRegistry){
        $produit = $rep->find($id);
        

    if ($produit) {

        $manager=$managerRegistry->getManager();
        $manager->remove($produit);
        $manager->flush();

        $this->addFlash(
            'info',
            'Produit supprimé avec succées'

        );
       
    }
       
    return $this->redirectToRoute('app_produit_admin');

    }


    /**
     * @Route("/produit/pdf",name="pdf_produit")
     */
    public function pdfProduit(ProduitShopRepository $rep){

          // Configure Dompdf according to your needs
          $pdfOptions = new Options();
          $pdfOptions->set('defaultFont', 'Arial');
          
          $pdfOptions->setIsRemoteEnabled(true);
          
          // Instantiate Dompdf with our options
          $dompdf = new Dompdf($pdfOptions);
          
          // Retrieve the HTML generated in our twig file
         $html =$this->renderView("produit_admin/listp.html.twig",[
            'produits' => $rep->findAll()
        ]);
          
          // Load HTML to Dompdf
          $dompdf->loadHtml($html);
          
          // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
          $dompdf->setPaper('A4', 'portrait');
  
          // Render the HTML as PDF
          $dompdf->render();
  
          // Output the generated PDF to Browser (inline view)
          $dompdf->stream("mypdf.pdf", [
              "Attachment" => true
          ]);
          
      


    }
}
