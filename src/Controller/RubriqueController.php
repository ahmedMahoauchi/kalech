<?php

namespace App\Controller;

use App\Entity\Rubrique;
use App\Form\RubriqueType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Serializer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class RubriqueController extends AbstractController
{
    /**
     * @Route("/rubrique", name="rubrique")
     */
    public function index(): Response
    {
        return $this->render('rubrique/index.html.twig', [
            'controller_name' => 'RubriqueController',
        ]);
    }

  /**
     * @Route("/listRubrique", name="listRubrique")
     */
    public function listRubrique()
    {
        $rubriques = $this->getDoctrine()->getRepository(Rubrique::class)->findAll();
        return $this->render('rubrique/list.html.twig', array("rubriques" => $rubriques));
    }
/**
     * @Route("/addRubrique", name="addRubrique")
     */
    public function addRubrique(Request $request)
    {
        $rubrique = new Rubrique();
        $form = $this->createForm(RubriqueType::class, $rubrique);
        $form->add('ajouter',SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($rubrique);
            $em->flush();
            return $this->redirectToRoute('listRubrique');
        }
        return $this->render("rubrique/addRubrique.html.twig",array('form'=>$form->createView()));
    }

      /**
     * @Route("/deleteRubrique/{id}", name="deleteRubrique")
     */
    public function deleteRubrique($id)
    {
        $rubrique = $this->getDoctrine()->getRepository(Rubrique::class)->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($rubrique);
        $em->flush();
        return $this->redirectToRoute("listRubrique");
    }

     /**
     * @Route("/updateRubrique/{id}", name="updateRubrique")
     */
    public function updateRubrique(Request $request,$id)
    {
        $rubrique = $this->getDoctrine()->getRepository(Rubrique::class)->find($id);
        $form = $this->createForm(RubriqueType::class, $rubrique);
        $form->add('modifier',SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute('listRubrique');
        }
        return $this->render("rubrique/updateRubrique.html.twig",array('form'=>$form->createView()));
    }

















    /**
     * @Route("/dis", name="dis")
     * @Method ({"GET","POST"})
     */
    public function all(NormalizerInterface $normalizer): Response
    {

        $em = $this->getDoctrine()->getManager();
        $blogs = $em->getRepository(Rubrique::class)->findAll();
        $datas = array();
        foreach ($blogs as $key => $blog) {
            $datas[$key]['id'] = $blog->getId();
            $datas[$key]['titre'] = $blog->getTitre();
        }
        return new JsonResponse($datas);

    }

    /**
     * @Route("/deleter", name="deleter")
     */
    public function deleterubriqueAction(Request $request): JsonResponse
    {
        $id = $request->get("id");

        $em = $this->getDoctrine()->getManager();
        $categ = $em->getRepository(Rubrique::class)->find($id);
        if($categ!=null ) {
            $em->remove($categ);
            $em->flush();
            $serialize = new Serializer([new ObjectNormalizer()]);
            $formatted = $serialize->normalize("rubrique a ete supprimee avec success.");
            return new JsonResponse($formatted);

        }
        return new JsonResponse("id rubrique invalide.");
    }

    /**
     * @Route("/modifierr/{id}", name="modifierr")
     * @Method("PUT")
     */
    public function modifierrubriqueAction(Request $request)
     {
         $id = $request->get("id");
        $em = $this->getDoctrine()->getManager();
        $produit = $this->getDoctrine()->getManager()
            ->getRepository(Rubrique::class)
            ->find($id);

        $produit->setTitre($request->get("titre"));


        $em->persist($produit);
        $em->flush();
         $encoders = [new JsonEncoder()];
         $normalizers = [new ObjectNormalizer()];
         $serializer = new Serializer($normalizers, $encoders);

        $formatted = $serializer->serialize($produit, 'json', [
            'circular_reference_handler' => function ($produit) {
                return $produit;
            }]);
        return new JsonResponse("rubrique a ete modifie avec success.");

    }
    /**
     * @Route("/ajouterr", name="ajouterr")
     * @Method("POST")
     */

    public function ajouterrubriqueAction(Request $request): JsonResponse
    {
        $categ = new Rubrique();
        $titre = $request->query->get("titre");
        $em = $this->getDoctrine()->getManager();
        $categ->setTitre($titre);

        $em->persist($categ);
        $em->flush();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($categ);
        return new JsonResponse($formatted);

    }




}
