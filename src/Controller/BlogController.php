<?php

namespace App\Controller;
use App\Entity\Blog;
use App\Entity\Rubrique;
use App\Entity\Comments;
use App\Entity\User;
use App\Form\BlogType;
use App\Repository\RubriqueRepository;
use App\Repository\UserRepository;
use App\Repository\BlogRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Constraints\DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Constraints\Json;
use Symfony\Component\HttpFoundation\JsonResponse;


class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="app_blog")
     */
    public function index(): Response
    {
        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
        ]);
    }
/**
     * @Route("/listBlog", name="listBlog")
     */
    public function listBlog()
    {
        $blogs = $this->getDoctrine()->getRepository(Blog::class)->findAll();
        return $this->render('blog/list.html.twig', array("blogs" => $blogs));
    }
        /**
     * @Route("/listcBlog", name="listcBlog")
     */
    public function listcBlog(PaginatorInterface $paginator,Request $request)
    { $em = $this->getDoctrine()->getManager();
        //$blogs = $this->getDoctrine()->getRepository(Blog::class)->findAll();
    $qb = $em->createQueryBuilder();

    $query = $qb->select('a', 'm')
            ->from('App\Entity\Blog', 'a')
            ->leftJoin('a.rubrique', 'm')
            ->getQuery();


        /**
         * @ var $paginator \Knp\Component\Pager\Paginator
         */
        //$paginator = $this->get('knp_paginator');
        $blogs = $paginator->paginate(
            $query,
            $request->query->getInt('page',1),
            $request->query->getInt('limit',3)
        );

        return $this->render('blog/listc.html.twig',[ "blogs" => $blogs]);
    }

    
/**
     * @Route("/addBlog", name="addBlog")
     */
    public function addBlog(Request $request)
    {
        $blog = new Blog();
        $form = $this->createForm(BlogType::class, $blog);
        $form->add('ajouter',SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $date = new \DateTime('@'.strtotime('now'));
        $blog->setDate($date);      
        $blog->setVotes(0);
         if($file = $form->get('photo')->getData()==NULL || $file = $form->get('photo')->getData()=='') {
            $blog->setPhoto('');
            }
            else {
        $file = $form->get('photo')->getData();
            //$file = $user->getimage();
            $fileName = md5(uniqid()).'.'.$file->guessExtension();
            try{
                $file->move(
                    $this->getParameter('uploads_directory'),
                    $fileName
                );
            }catch(FileException $e){

            }
            $blog->setPhoto($fileName);
        }
            $em = $this->getDoctrine()->getManager();
           
            $em->persist($blog);
            $em->flush();
            
            return $this->redirectToRoute('listBlog');
        }
        return $this->render("blog/addBlog.html.twig",array('form'=>$form->createView()));
    }


    
    /**
     * @Route("/addcBlog", name="addcBlog")
     */
    public function addcBlog(Request $request)
    {
        $blog = new Blog();
        $form = $this->createForm(BlogType::class, $blog);
        $form->add('ajouter',SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $date = new \DateTime('@'.strtotime('now'));
        $blog->setDate($date);      
        $blog->setVotes(0);
         if($file = $form->get('photo')->getData()==NULL || $file = $form->get('photo')->getData()=='') {
            $blog->setPhoto('');
            }
            else {
        $file = $form->get('photo')->getData();
            //$file = $user->getimage();
            $fileName = md5(uniqid()).'.'.$file->guessExtension();
            try{
                $file->move(
                    $this->getParameter('uploads_directory'),
                    $fileName
                );
            }catch(FileException $e){

            }
            $blog->setPhoto($fileName);
        }
            $em = $this->getDoctrine()->getManager();
            $em->persist($blog);
            $em->flush();

            return $this->redirectToRoute('listcBlog');
        }
        return $this->render("blog/addcBlog.html.twig",array('form'=>$form->createView()));
    }
/**
     * @Route("/deleteBlog/{id}", name="deleteBlog")
     */
    public function deleteBlog($id)
    {
        $blog = $this->getDoctrine()->getRepository(Blog::class)->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($blog);
        $em->flush();
        return $this->redirectToRoute("listBlog");
    }
    /**
     * @Route("/deletecBlog/{id}", name="deletecBlog")
     */
    public function deletecBlog($id)
    {
        $blog = $this->getDoctrine()->getRepository(Blog::class)->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($blog);
        $em->flush();
        return $this->redirectToRoute("listcBlog");
    }

    /**
     * @Route("/updateBlog/{id}", name="updateBlog")
     */
    public function updateBlog(Request $request,$id)
    {
        $blog = $this->getDoctrine()->getRepository(Blog::class)->find($id);
        $form = $this->createForm(BlogType::class, $blog);
        $form->add('modifier',SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
   
            if($file = $form->get('photo')->getData()==NULL || $file = $form->get('photo')->getData()=='') {
                $blog->setPhoto('');
                }
                else {
            $file = $form->get('photo')->getData();
                //$file = $user->getimage();
                $fileName = md5(uniqid()).'.'.$file->guessExtension();
                try{
                    $file->move(
                        $this->getParameter('uploads_directory'),
                        $fileName
                    );
                }catch(FileException $e){
    
                }
                $blog->setPhoto($fileName);
            }

            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute('listBlog');
        }
        return $this->render("blog/updateBlog.html.twig",array('form'=>$form->createView()));
    }
     /**
     * @Route("/updatecBlog/{id}", name="updatecBlog")
     */
    public function updatecBlog(Request $request,$id)
    {
        $blog = $this->getDoctrine()->getRepository(Blog::class)->find($id);
        $form = $this->createForm(BlogType::class, $blog);
        $form->add('modifier',SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
   
            if($file = $form->get('photo')->getData()==NULL || $file = $form->get('photo')->getData()=='') {
                $blog->setPhoto('');
                }
                else {
            $file = $form->get('photo')->getData();
                //$file = $user->getimage();
                $fileName = md5(uniqid()).'.'.$file->guessExtension();
                try{
                    $file->move(
                        $this->getParameter('uploads_directory'),
                        $fileName
                    );
                }catch(FileException $e){
    
                }
                $blog->setPhoto($fileName);
            }

            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute('listcBlog');
        }
        return $this->render("blog/updatecBlog.html.twig",array('form'=>$form->createView()));
    }


    /**
     * @Route("/showdetailedAction/{id}", name="showdetailedAction")
     */
    public function showdetailedAction(Request $request,$id)
    { 
        $em= $this->getDoctrine()->getManager();
        $blogs = $this->getDoctrine()->getRepository(Blog::class)->find($id);
        $commentss = $this->getDoctrine()->getRepository(Comments::class)->findAll();
        $allcomments = $this->getDoctrine()->getRepository(Comments::class)->findAll();
        $replies = $this->getDoctrine()->getRepository(Comments::class)->findAll();
        return $this->render('blog/detailedpost.html.twig', array("blogs" => $blogs,"allcomments" => $allcomments,"commentss" => $commentss,"replies" => $replies));
        
    }


 /**
     * @Route("/search", name="ajax_search")
     */
    public function searchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $requestString = $request->get('q');
        $blogs =  $em->getRepository(Blog::class)->findEntitiesByString($requestString);
        if(!$blogs) {
            $result['blogs']['error'] = "Post Not found :( ";
        } else {
            $result['blogs'] = $this->getRealEntities($blogs);
        }
        return new Response(json_encode($result));
    }
    public function getRealEntities($blogs){
        foreach ($blogs as $blogs){
            $realEntities[$blogs->getId()] = [$blogs->getPhoto(),$blogs->getTitre()];

        }
        return $realEntities;
    }









       //Partie JSON/////////////////////////////////////////

 /**
     * @Route("/disp", name="disp")
     * Method ({"GET","POST"})
     */
    public function all(): Response
    {

        $em = $this->getDoctrine()->getManager();
        $blogs = $em->getRepository(Blog::class)->findAll();
        $datas = array();
        foreach ($blogs as $key => $blog) {
            $datas[$key]['id'] = $blog->getId();
            $datas[$key]['titre'] = $blog->getTitre();
            $datas[$key]['description'] = $blog->getDescription();
            $datas[$key]['date'] = $blog->getDate();
            $datas[$key]['votes'] = $blog->getVotes();
            $datas[$key]['photo'] = $blog->getPhoto();
            $datas[$key]['idUser'] = $blog->getIdUser()->getNom();
            $datas[$key]['rubrique'] = $blog->getRubrique()->getTitre();



        }
        return new JsonResponse($datas);

    }
    /**
     * @Route("/deletecc", name="deletecc")
     */
    public function deletecontratAction(Request $request): JsonResponse
    {

        $id = $request->get("id");

        $em = $this->getDoctrine()->getManager();
        $categ = $em->getRepository(Blog::class)->find($id);
        if($categ!=null ) {
            $em->remove($categ);
            $em->flush();
            $serialize = new Serializer([new ObjectNormalizer()]);
            $formatted = $serialize->normalize("Blog a ete supprimee avec success.");
            return new JsonResponse($formatted);

        }
        return new JsonResponse("id Blog invalide.");
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
        $produit->setDescription($request->get("description"));


        $em->persist($produit);
        $em->flush();
         $encoders = [new JsonEncoder()];
         $normalizers = [new ObjectNormalizer()];
         $serializer = new Serializer($normalizers, $encoders);

        $formatted = $serializer->serialize($produit, 'json', [
            'circular_reference_handler' => function ($produit) {
                return $produit;
            }]);
        return new JsonResponse("Blog a ete modifie avec success.");

    }
    /**
     * @Route("/ajoutC", name="ajoutC")
     * @Method("POST")
     */

    public function ajoutercontratAction(Request $request): JsonResponse
    {
        $categ = new Blog();
        $description = $request->query->get("description");
        $titre = $request->query->get("titre");
        $date = new \DateTime('now');
        $votes = $request->query->get("votes");
        $photo = $request->query->get("photo");
        $nomr = $request->query->get("rubrique");
        


            $rubrique = $this->getDoctrine()->getManager()
            ->getRepository(Rubrique::class)
            ->findOneBy(['titre' => $nomr]);

        $em = $this->getDoctrine()->getManager();
        $categ->setContenu($description);
        $categ->setTitre($titre);
        $categ->setDate($date);
        $categ->setVotes(0);
        $categ->setPhoto("");
        $categ->setIdUser(1);
        $categ->setRubrique($rubrique);
        $em->persist($categ);
        $em->flush();
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        $formatted = $serializer->serialize($categ, 'json', [
            'circular_reference_handler' => function ($categ) {
                return $categ;
            }]);
        return new JsonResponse($formatted);


    }




}
