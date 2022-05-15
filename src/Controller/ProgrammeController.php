<?php

namespace App\Controller;

use App\Entity\Programme;
use App\Entity\User;
use App\Form\ProgrammeType;
use App\Form\SendEmailType;
use App\Repository\ProgrammeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\File;
use App\Controller\pathinfo;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Knp\Component\Pager\PaginatorInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Swift_Mailer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Validator\Constraints\Json;
/**
 * @Route("/programme")
 */
class ProgrammeController extends AbstractController
{
    //Partie JSON/////////////////////////////////////////

    /**
     * @Route("/ajoutEJSON", name="ajoutEJSON")
     * @Method("POST")
     */
    public function ajoutEJSON(Request  $request): Response
    {   //Création du formulaire
        $e=new Programme();
        $u=new User();
        $u->setIduseru(1);
        $form=$this->createForm(ProgrammeType::class,$e);
        //recupérer les données saisies depuis la requete http
        $form->handleRequest($request);
        $nomprogramme = $request->query->get("nomprogramme");
        $niveauprogramme = $request->query->get("niveauprogramme");
        $genreprogramme = $request->query->get("genreprogramme");
        $typeprogramme = $request->query->get("typeprogramme");
        $descriptionprogramme = $request->query->get("descriptionprogramme");
        $imageprogramme = $request->query->get("imageprogramme");
        $iduser = $request->query->get("iduser");
        $user = $this->getDoctrine()->getManager()
            ->getRepository(User::class)
            ->findOneBy(['id' => $iduser]);
   
         $em=$this->getDoctrine()->getManager();
        $e->setNomprogramme($nomprogramme);
        $e->setDescriptionprogramme($descriptionprogramme);
        $e->setNiveauprogramme($niveauprogramme);
        $e->setGenreprogramme($genreprogramme);
        $e->setTypeprogramme($typeprogramme);
        $e->setImageprogramme($imageprogramme);
        $e->setIduser($user);

            $em->persist($e);
            $em->flush();
            $serializer = new Serializer([new ObjectNormalizer()]);
            $formatted = $serializer->normalize($e);
            return new JsonResponse($formatted);
    }
        /**
     * 
     * @Route("/displayEvenementJSON", name="displayEvenementJSON")
     * @Method("GET")
     */
    public function allEVAction()
    {

        $Programme = $this->getDoctrine()->getManager()->getRepository(Programme::class)->findAll();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($Programme);

        return new JsonResponse($formatted);

    }
      /******************Modifier Programme*****************************************/
    /**
     * @Route("/updateProgramme", name="update_programme")
     * @Method("PUT")
     */
    public function modifierEvenementAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $Programme = $this->getDoctrine()->getManager()
            ->getRepository(Programme::class)
            ->find($request->get("idprogramme"));
        $Programme->setNomprogramme($request->get("nomprogramme"));
        $Programme->setDescriptionprogramme($request->get("descriptionprogramme"));
        $Programme->setNiveauprogramme($request->get("niveauprogramme"));

        $em->persist($Programme);
        $em->flush();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($Programme);
        return new JsonResponse("Programme a ete modifiee avec success.");

    }
    /**
     * @Route("/deleteProgramme", name="delete_programme")
     * @Method("DELETE")
     */

    public function deleteProgrammeAction(Request $request) {
        $id = $request->get("idprogramme");

        $em = $this->getDoctrine()->getManager();
        $programme = $em->getRepository(Programme::class)->find($id);
        if($programme!=null ) {
            $em->remove($programme);
            $em->flush();

            $serialize = new Serializer([new ObjectNormalizer()]);
            $formatted = $serialize->normalize("Programme a ete supprimee avec success.");
            return new JsonResponse($formatted);

        }
        return new JsonResponse("id Evenement invalide.");


    }

    /**
     * @Route("/", name="app_programme_index", methods={"GET"})
     */
    public function index(EntityManagerInterface $entityManager): Response
    {
        $programmes = $entityManager
            ->getRepository(Programme::class)
            ->findAll();

        return $this->render('programme/index.html.twig', [
            'programmes' => $programmes,
        ]);
    }
    /**
     * @Route("/programmepdf/{idprogramme}", name="programme_pdf", methods={"GET"})
     */
   
    public function programmepdf(Programme $programme): Response
    {
    $pdfOptions = new Options();
    $pdfOptions->set('defaultFont', 'Arial');
    
    // Instantiate Dompdf with our options
    $dompdf = new Dompdf($pdfOptions);
       
    // Retrieve the HTML generated in our twig file
    $html = $this->renderView('programme/programmepdf.html.twig', [
        'programme' => $programme,
    ]);
    
    // Load HTML to Dompdf
    $dompdf->loadHtml($html);
    
    // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
    $dompdf->setPaper('A4', 'portrait');

    // Render the HTML as PDF
    $dompdf->render();

    // Output the generated PDF to Browser (force download)
    $dompdf->stream("mypdf.pdf", [
        "Attachment" => false
    ]);

}
    /**
     * @Route("/prog", name="app_programme_index1", methods={"GET"})
     */
    public function index1(ProgrammeRepository $programmeRepository,Request $request, PaginatorInterface $paginator): Response
    {
        $programmes=$programmeRepository->findAll();
        $programmes = $paginator->paginate(
            $programmes, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            4 // Nombre de résultats par page
        );
        return $this->render('programme/indexFront.html.twig', [
            'programmes' => $programmes,
        ]);
    }

    /**
     * @Route("/new", name="app_programme_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $programme = new Programme();
        $form = $this->createForm(ProgrammeType::class, $programme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('imageprogramme')->getData();
            //$file = $user->getimage();
            $fileName = md5(uniqid()).'.'.$file->guessExtension();
            try{
                $file->move(
                    $this->getParameter('uploads_directory'),
                    $fileName
                );
            }catch(FileException $e){

            }
            $entityManager = $this->getDoctrine()->getManager();
            $programme->setImageprogramme($fileName);
            $entityManager->persist($programme);
            $entityManager->flush();

            return $this->redirectToRoute('app_programme_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('programme/new.html.twig', [
            'programme' => $programme,
            'form' => $form->createView(),
        ]);
    }
    
        /**
         * @Route("/sendemail", name="sendemail")
         */
        public function sendmail(Request $request,\Swift_Mailer $mailer)
        {
            $SendEmailForm = $this->createForm(SendEmailType::class);
            $SendEmailForm->handleRequest($request);
    
            if ($SendEmailForm->isSubmitted() && $SendEmailForm->isValid()) {
                $contact = $SendEmailForm->getData();
                $file = $SendEmailForm->get('imageprogramme')->getData();
                $fileName = md5(uniqid()).'.'.$file->guessExtension();
                 
               // dd($finder);
                // On crée le message
                $message = (new \Swift_Message('Nouveau contact'))
                // On attribue l'expéditeur
                ->setFrom('pidev.3eme@gmail.com')
                // On attribue le destinataire
                ->setTo($contact['email'])
                // On crée le texte avec la vue
                ->setBody(
                    $this->renderView(
                        'programme/mailbody.html.twig', compact('contact')
                    ),
                    'text/html'
                )
               // ->attach(Swift_Attachment::fromPath($file->pathinfo))
                ;
                $mailer->send($message);
    
                $this->addFlash('message', 'le mail a bien été envoyer'); // Permet un message flash de renvoi
                return $this->redirectToRoute('app_programme_index');
            }
            return $this->render('programme/SendEmail.html.twig',['SendEmailForm' => $SendEmailForm->createView()]);
        }
    
    
    /**
     * @Route("/{idprogramme}", name="app_programme_show", methods={"GET"})
     */
    public function show(Programme $programme): Response
    {
        return $this->render('programme/show.html.twig', [
            'programme' => $programme,
        ]);
    }
    /**
     * @Route("/prog/{idprogramme}", name="app_programme_show1", methods={"GET"})
     */
    public function show1(Programme $programme): Response
    {
        return $this->render('programme/showfront.html.twig', [
            'programme' => $programme,
        ]);
    }

    /**
     * @Route("/{idprogramme}/edit", name="app_programme_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Programme $programme, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProgrammeType::class, $programme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('imageprogramme')->getData();
            //$file = $user->getimage();
            $fileName = md5(uniqid()).'.'.$file->guessExtension();
            try{
                $file->move(
                    $this->getParameter('uploads_directory'),
                    $fileName
                );
            }catch(FileException $e){

            }
            $programme->setImageprogramme($fileName);
            $entityManager->flush();

            return $this->redirectToRoute('app_programme_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('programme/edit.html.twig', [
            'programme' => $programme,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{idprogramme}", name="app_programme_delete", methods={"POST"})
     */
    public function delete(Request $request, Programme $programme, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$programme->getIdprogramme(), $request->request->get('_token'))) {
            $entityManager->remove($programme);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_programme_index', [], Response::HTTP_SEE_OTHER);
    }
    /**
     * @Route("/order1/{searchString}", name="order")
     */
    public function orderEnt($searchString,SerializerInterface $serializer)
    {
        
        //$serializer = new Serializer([new ObjectNormalizer()]);
      
        $repository = $this->getDoctrine()->getRepository(Programme::class);
        $programmes = $repository->findByEx($searchString);
        $data = $serializer->normalize($programmes,'json',['attributes' => ['idprogramme', 'nomprogramme', 'descriptionprogramme',
        'niveauprogramme', 'genreprogramme', 'typeprogramme', 'imageprogramme']]);
       // return new JsonResponse($data);
        return $this->render('programme/index.html.twig', [
            'programmes' => $programmes,
        ]);
    }
     /**
     * @Route("/order2/{searchString}", name="order2")
     */
    public function orderEnt2($searchString,SerializerInterface $serializer)
    {
        
        //$serializer = new Serializer([new ObjectNormalizer()]);
      
        $repository = $this->getDoctrine()->getRepository(Programme::class);
        $programmes = $repository->findByEx2($searchString);
        $data = $serializer->normalize($programmes,'json',['attributes' => ['idprogramme', 'nomprogramme', 'descriptionprogramme',
        'niveauprogramme', 'genreprogramme', 'typeprogramme', 'imageprogramme']]);
       // return new JsonResponse($data);
        return $this->render('programme/index.html.twig', [
            'programmes' => $programmes,
        ]);
    }
    /**
     * @Route("/search1/{searchString}", name="search")
     */
    public function searchEnt($searchString,SerializerInterface $serializer)
    {
        
        //$serializer = new Serializer([new ObjectNormalizer()]);
      
        $repository = $this->getDoctrine()->getRepository(Programme::class);
        $users = $repository->findByExampleField($searchString);
        $data = $serializer->normalize($users,'json',['attributes' => ['idprogramme', 'nomprogramme', 'descriptionprogramme',
         'niveauprogramme', 'genreprogramme', 'typeprogramme', 'imageprogramme']]);
        return new JsonResponse($data);
    }
    

   
}
