<?php

namespace App\Controller;

use App\Entity\Reclammation;
use App\Form\ReclammationType;
use App\Repository\ReclammationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ReclammationController extends AbstractController
{
  /**
     * @Route("/reclammation", name="recla_create")
     */

    public function create(Request $request, ManagerRegistry $managerRegistry)
    {  
        $recla = new Reclammation();
        $form = $this->createForm(ReclammationType::class,$recla);
        $form->handleRequest($request);
        dump($recla);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $manager=$managerRegistry->getManager();
            $manager->persist($recla);
            $manager->flush();
            $this->addFlash(
                'info',
                'Reclammation Soumisse'
               );
            return $this->redirectToRoute('recla_create');
        }
        return $this->render('reclammation/create.html.twig', [
            'formRecla' => $form->createView()
        ]);
    }
     /**
     * @Route("/reclammationJson", name="recla_create2")
     */

    public function create2(Request $request, ManagerRegistry $managerRegistry)
    {  
        $recla = new Reclammation();
       $nom = $request->query->get("nom");
       $theme = $request->query->get("theme");
       $date = $request->query->get("date");
       $description = $request->query->get("description");

       $recla->setNom($nom);
       $recla->setTheme($theme);
       $recla->setDate($date);
       $recla->setDescription($description);

       $manager=$managerRegistry->getManager();
       $manager->persist($recla);
       $manager->flush();
     $serializer = new Serializer([new ObjectNormalizer()]);
     $formated = $serializer->normalize($recla);
     return new JsonResponse($formated);
    }
     /**
     * @Route("/admin/reclammation/mod/{id}", name="uu")
     */
    function update(Request $request,ReclammationRepository $repo,$id)
    {
        $reclas=$repo->find($id);
        $form =$this->createForm(ReclammationType::class,$reclas);
        $form->add('Modifier',SubmitType::class);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $em=$this->getDoctrine()->getManager();

            $em->flush();
            $this->addFlash(
                'info',
                'Reclammation Modifiée'
               );
            return $this->redirectToRoute('app_admin_reclammation');
        }
        return $this->render('admin_reclammation/modifier.html.twig',[
            'formRecla' => $form->createView()
        ]);
    }

    /**
     * @Route("/mod1Json", name="uu2")
     */
    function updateJson(Request $request)
    {
        $em=$this->getDoctrine()->getManager();
        $recla= $this->getDoctrine()->getManager()
                  ->getRepository(Reclammation::class)
                  ->find($request->get("id"));
                
$recla->setNom     ($request->get("nom"));
$recla->setTheme  ($request->get("theme"));
$recla->setDate    ($request->get("date"));
$recla->setDescription   ($request->get("description"));

    

$em->persist($recla);
$em->flush();
$serializer = new Serializer([new ObjectNormalizer()]);
$formated = $serializer->normalize($recla);
return new JsonResponse("modifié avec success");
     
    }
 /**
     * @Route("/SuppJson/{id}", name="d22")
     */
    public function Delete($id)
    {
      
    $em= $this->getDoctrine()->getManager();
    $recla =$em->getRepository(Reclammation::class)->find($id);
    $em->remove($recla);
    $em->flush();
    $serializer = new Serializer([new ObjectNormalizer()]);
$formated = $serializer->normalize($recla);
return new JsonResponse($formated);
    }
     /**
     * @Route("/admin/recla1Json", name="app22_admin_client")
     */
    public function index(ReclammationRepository $repo, NormalizerInterface $Normalizer): Response
    {
        $reclas = $repo->findAll();
        $jsonContent = $Normalizer->normalize($reclas, 'Json',['groups'=>'post:read']);
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formated = $serializer->normalize($reclas);
        return new JsonResponse($formated);
    }

 /**
 * @Route("/pdf2", name="pdfrecla")
 */
public function pdf()
{
    // Configure Dompdf according to your needs
    $repository=$this->getDoctrine()->getRepository(Reclammation::class); 
    $pdfOptions = new Options();
    $pdfOptions->set('defaultFont', 'Times New Roman');

    // Instantiate Dompdf with our options
    $dompdf = new Dompdf($pdfOptions);
    $recla=$repository->findAll();
    //l'image est située au niveau du dossier public
    $png = file_get_contents("logo.png");
    $pngbase64 = base64_encode($png);
    // Retrieve the HTML generated in our twig file
    $html = $this->renderView('reclammation/pdf.html.twig', [
         "img64"=>$pngbase64,
         'recla'=>$recla
    ]);
//  $html = $this->renderView('client/pdf.html.twig', [
//      'client'=>$client
// ]);

    // Load HTML to Dompdf
    $dompdf->loadHtml($html);

    // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
    $dompdf->setPaper('A4', 'portrait');

    // Render the HTML as PDF
    $dompdf->render();

    // Output the generated PDF to Browser (force download)
    $dompdf->stream("reclammation.pdf", [
        "Attachment" => false
    ]);
}
}
