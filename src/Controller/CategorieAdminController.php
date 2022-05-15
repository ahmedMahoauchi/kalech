<?php

namespace App\Controller;

use App\Entity\CategorieProduit;
use App\Form\CategoryType;
use App\Repository\CategorieProduitRepository;
use App\Repository\ProduitShopRepository;
use Doctrine\Persistence\ManagerRegistry;
use SebastianBergmann\CodeCoverage\Report\Html\Renderer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Serializer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class CategorieAdminController extends AbstractController
{
    /**
     * @Route("/categorie/admin", name="app_categorie_admin")
     */
    public function index(Request $request,CategorieProduitRepository $repCat,ProduitShopRepository $repProd,ManagerRegistry  $managerRegistry): Response
    {

        $categories = $repCat->findAll();

        $catNom =[];
        $prodCount = [];
        $catColor= [];
 
        foreach ($categories as $cat) {
            $catNom[] = $cat->getNomCategorie();
            $prodCount[] =count($repProd->find4RelatedProducts($cat->getId())) ;
            $catColor[] = '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
        }

        $categorie = new CategorieProduit();
        $form = $this->createForm(CategoryType::class,$categorie);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
           
           
            $manager=$managerRegistry->getManager();
            $manager->persist($categorie);
            $manager->flush();

            $this->addFlash(
                'info',
                'categorie ajoutée avec succés'

            );
        }


        return $this->render('categorie_admin/index.html.twig', [
            'controller_name' => 'CategorieAdminController',
            'categories' => $categories,
            'formCategorie' => $form->createView(),
            'catNom' => json_encode($catNom),
            'prodCount' => json_encode($prodCount),
            'catColor' => json_encode($catColor)
        ]);
    }
    /**
     * @Route("/categorie/admin/modifer/{id}",name="Modifer_Categorie")
     */

    public function modifierCategorie($id){

        $categorie = $this->getDoctrine()
        ->getRepository(Product::class)
        ->find($id);

    if (!$categorie) {
        throw $this->createNotFoundException(
            'No categorie found for id '.$id
        );
    }else{
        
    }
    

    // or render a template
    // in the template, print things with {{ product.name }}
    // return $this->render('product/show.html.twig', ['product' => $product]);

    }

     /**
     * @Route("/categorie/admin/supprimer/{id}",name="Supprimer_Categorie")
     */ 

    public function supprimerCategorie($id,CategorieProduitRepository $rep, ManagerRegistry  $managerRegistry){
        $categorie = $rep->find($id);
        

    if ($categorie) {

        $manager=$managerRegistry->getManager();
        $manager->remove($categorie);
        $manager->flush();

        $this->addFlash(
            'info',
            'categorie supprimée avec succées'

        );
    }
       

    return $this->redirectToRoute('app_categorie_admin');

    }

    /*************************JSON**************************** */







    /**
     * @Route("/disc", name="disc")
     * @Method ({"GET","POST"})
     */
    public function all(NormalizerInterface $normalizer): Response
    {

        $em = $this->getDoctrine()->getManager();
        $blogs = $em->getRepository(CategorieProduit::class)->findAll();
        $datas = array();
        foreach ($blogs as $key => $blog) {
            $datas[$key]['id'] = $blog->getId();
            $datas[$key]['nomCategorie'] = $blog->getNomCategorie();
            $datas[$key]['descriptionCategorie'] = $blog->getDescriptionCategorie();
            
        }
        return new JsonResponse($datas);

    }

    /**
     * @Route("/deletec", name="deletec")
     */
    public function deleterubriqueAction(Request $request): JsonResponse
    {
        $id = $request->get("id");

        $em = $this->getDoctrine()->getManager();
        $categ = $em->getRepository(CategorieProduit::class)->find($id);
        if($categ!=null ) {
            $em->remove($categ);
            $em->flush();
            $serialize = new Serializer([new ObjectNormalizer()]);
            $formatted = $serialize->normalize("categorie a ete supprimee avec success.");
            return new JsonResponse($formatted);

        }
        return new JsonResponse("id categorie invalide.");
    }

    /**
     * @Route("/modifierc/{id}", name="modifierc")
     * @Method("PUT")
     */
    public function modifierrubriqueAction(Request $request)
     {
         $id = $request->get("id");
        $em = $this->getDoctrine()->getManager();
        $produit = $this->getDoctrine()->getManager()
            ->getRepository(CategorieProduit::class)
            ->find($id);

        $produit->setNomCategorie($request->get("nomCategorie"));
        $produit->setDescriptionCategorie($request->get("descriptionCategorie"));

        $em->persist($produit);
        $em->flush();
         $encoders = [new JsonEncoder()];
         $normalizers = [new ObjectNormalizer()];
         $serializer = new Serializer($normalizers, $encoders);

        $formatted = $serializer->serialize($produit, 'json', [
            'circular_reference_handler' => function ($produit) {
                return $produit;
            }]);
        return new JsonResponse("categorie a ete modifie avec success.");

    }
    /**
     * @Route("/ajouterc", name="ajouterc")
     * @Method("POST")
     */

    public function ajouterrubriqueAction(Request $request): JsonResponse
    {
        $categ = new CategorieProduit();
        $nom = $request->query->get("nomCategorie");
        $description = $request->query->get("descriptionCategorie");
        $em = $this->getDoctrine()->getManager();
        $categ->setNomCategorie($nom);
        $categ->setDescriptionCategorie($description);

        $em->persist($categ);
        $em->flush();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($categ);
        return new JsonResponse($formatted);

    }

    
}
