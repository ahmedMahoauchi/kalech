<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\ClientType;
use App\Repository\ClientRepository;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ClientController extends AbstractController
{
    /**
     * @Route("/client", name="client")
     */
    public function index()
    {
        $repo = $this->getDoctrine()->getRepository(Client::class);
        $client = $repo->findAll();
        return $this->render('client/index.html.twig', [
            'controller_name' => 'ClientController',
            'client' => $client
        ]);
    }
   /**
   * @Route("/client/new",name="client_create")
   */
   public function create(Request $request, ManagerRegistry $managerRegistry)
   {
    
       $client = new Client();
 $form = $this->createForm(ClientType::class,$client);
 $form->handleRequest($request);
 dump($client);
if ($form->isSubmitted()&& $form->isValid()) {
   $manager=$managerRegistry->getManager();
   $manager->persist($client);
   $manager->flush();

   $this->addFlash(
    'info',
    'Bienvenu chez Appollon Gym'
   );
   return $this->redirectToRoute('client_create');
} 
 return $this->render('client/create.html.twig',[
     'formClient' => $form->createView()
 ]);
 
   }
   
    /**
   * @Route("/client/newJson",name="client_create2")
   */
  public function create2(Request $request, ManagerRegistry $managerRegistry)
  {
   
      $client = new Client();
$nom = $request->query->get("nom");
$prenom = $request->query->get("prenom");
$date = $request->query->get("date");
$sexe = $request->query->get("sexe");
$num = $request->query->get("num");
$email = $request->query->get("email");
$password = $request->query->get("password");

$client->setNom($nom);
$client->setPrenom($prenom);
$client->setDate($date);
$client->setSexe($sexe);
$client->setNum($num);
$client->setEmail($email);
$client->setPassword($password);

dump($client);

  $manager=$managerRegistry->getManager();
  $manager->persist($client);
  $manager->flush();
$serializer = new Serializer([new ObjectNormalizer()]);
$formated = $serializer->normalize($client);
return new JsonResponse($formated);

  }


   /**
     * @Route("/admin/client/mod/{id}", name="u")
     */
    function update(Request $request,ClientRepository $repo,$id)
    {
        $clients=$repo->find($id);
        $form =$this->createForm(ClientType::class,$clients);
        $form->add('Modifier',SubmitType::class);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $em=$this->getDoctrine()->getManager();

            $em->flush();
            $this->addFlash(
                'info',
                'Modification accord??e'
               );
            return $this->redirectToRoute('app_admin_client');
        }
        return $this->render('admin_client/modifier.html.twig',[
            'formClient' => $form->createView()
        ]);
    }
     /**
     * @Route("/modJson", name="u2")
     * 
     */
    function updateJson(Request $request)
    {
        $em=$this->getDoctrine()->getManager();
        $client= $this->getDoctrine()->getManager()
                  ->getRepository(Client::class)
                  ->find($request->get("id"));
                
$client->setNom     ($request->get("nom"));
$client->setPrenom  ($request->get("prenom"));
$client->setDate    ($request->get("date"));
$client->setSexe    ($request->get("sexe"));
$client->setNum     ($request->get("num"));
$client->setEmail   ($request->get("email"));
$client->setPassword($request->get("password"));
    

$em->persist($client);
$em->flush();
$serializer = new Serializer([new ObjectNormalizer()]);
$formated = $serializer->normalize($client);
return new JsonResponse("modifi?? avec success");
     
    }

    /**
     * @Route("/admin/client/SuppJson/{id}", name="d2")
     */
    public function Delete($id)
    {
      
    $em= $this->getDoctrine()->getManager();
    $client =$em->getRepository(Client::class)->find($id);
    $em->remove($client);
    $em->flush();
    $serializer = new Serializer([new ObjectNormalizer()]);
$formated = $serializer->normalize($client);
return new JsonResponse($formated);
    }
    /**
     * @Route("/admin/clientJson", name="app2_admin_client")
     */
    public function index2(ClientRepository $repo, NormalizerInterface $Normalizer): Response
    {
        $clients = $repo->findAll();
        $jsonContent = $Normalizer->normalize($clients, 'Json',['groups'=>'post:read']);
        $serializer = new Serializer([new ObjectNormalizer()]);
$formated = $serializer->normalize($clients);
return new JsonResponse($formated);
    }
}
