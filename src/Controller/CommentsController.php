<?php

namespace App\Controller;
use App\Entity\Rubrique;
use App\Entity\Blog;
use App\Entity\Comments;
use App\Entity\User;
use App\Form\CommentsType;
use App\Form\BlogType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;
use MercurySeries\FlashyBundle\FlashyNotifier;

class CommentsController extends AbstractController
{
    /**
     * @Route("/comments", name="app_comments")
     */
    public function index(): Response
    {
        return $this->render('comments/index.html.twig', [
            'controller_name' => 'CommentsController',
        ]);
    }

    /**
     * @Route("/listComments", name="listComments")
     */
    public function listComments()
    {
        $commentss = $this->getDoctrine()->getRepository(Comments::class)->findAll();
        return $this->render('comments/list.html.twig', array("commentss" => $commentss));
    }

/**
     * @Route("/deleteComments/{id}/{d}/{m}", name="deleteComments")
     */
    public function deleteComments(Request $request,$id,$d,$m)
    {
        $comments = $this->getDoctrine()->getRepository(Comments::class)->find($id);
        $em = $this->getDoctrine()->getManager();

        try {
            $email = (new Email())
                ->from("pidev.3eme@gmail.com")
                ->to("$m")
                ->subject("Commentaire supprimé")
                ->text("le commentaire '$d' a violé notre TOS et a été supprimé");

            $this->mailer->send($email);
        } catch (TransportException $e) {
            print $e->getMessage()."\n";
            throw $e;
        }



        $em->remove($comments);
        $em->flush();

        $ref = $request->headers->get('referer');
        return $this->redirect($ref);
    }
/**
     * @Route("/deletecComments/{id}", name="deletecComments")
     */
    public function deletecComments(Request $request,$id)
    {
        $comments = $this->getDoctrine()->getRepository(Comments::class)->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($comments);
        $em->flush();

        $ref = $request->headers->get('referer');
        return $this->redirect($ref);
    }



/**
     * @Route("/addComments/{id}", name="addComments")
     */
    public function addComments(Request $request,$id)
    {
        $comments = new Comments();
        $form = $this->createForm(CommentsType::class, $comments);
        $form->add('ajouter',SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $date = new \DateTime('@'.strtotime('now'));
        $comments->setDate($date);      
        $comments->setVotes(0);
        $comments->setIdParentComment(0);

        $post = $this->getDoctrine()
        ->getRepository(Blog::class)
        ->find($request->get("id"));


        $comments->setIdBlog($post);
     
        
            
        
        $em= $this->getDoctrine()->getManager();
  
       
        
            $em->persist($comments);
            $em->flush();
            return $this->redirectToRoute('showdetailedAction', [
                'id' => $id
            ]);
        }
        return $this->render("comments/addComments.html.twig",array('form'=>$form->createView()));
   
}
/**
     * @Route("/addReplies/{id}/{idc}", name="addReplies")
     */
    public function addReplies(Request $request,$id,$idc)
    {
        $comments = new Comments();
        $form = $this->createForm(CommentsType::class, $comments);
        $form->add('ajouter',SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $date = new \DateTime('@'.strtotime('now'));
        $comments->setDate($date);      
        $comments->setVotes(0);
 

        $post = $this->getDoctrine()
        ->getRepository(Blog::class)
        ->find($request->get("id"));


        $comments->setIdBlog($post);

     
        $comments->setIdParentComment($idc);      
            
        
        $em= $this->getDoctrine()->getManager();
  
       
        
            $em->persist($comments);
            $em->flush();
            return $this->redirectToRoute('showdetailedAction', [
                'id' => $id
            ]);
        }
        return $this->render("comments/addReplies.html.twig",array('form'=>$form->createView()));
   
}


   /**
     * @param string $subject
     * @param string $from
     * @param string $to
     * @param string $template
     * @param array $parameters
     * @throws TransportExceptionInterface
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function send(string $subject, string $from, string $to, string $template, array $parameters): void
    {
        try {
            $email = (new Email())
                ->from($from)
                ->to($to)
                ->subject($subject)
                ->html(
                    $this->twig->render($template, $parameters)
                );

            $this->mailer->send($email);
        } catch (TransportException $e) {
            print $e->getMessage()."\n";
            throw $e;
        }

    }
    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * @var Environment
     */
    private $twig;

    /**
     * MailerService constructor.
     *
     * @param MailerInterface       $mailer
     * @param Environment   $twig
     */
    public function __construct(MailerInterface $mailer, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

}
