<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\ClientType;
use App\Repository\ClientRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
use Dompdf\Options;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\PieChart;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class AdminClientController extends AbstractController
{
    /**
     * @Route("/admin/client", name="app_admin_client")
     */
    public function index(ClientRepository $repo, NormalizerInterface $Normalizer): Response
    {
        $clients = $repo->findAll();
        $jsonContent = $Normalizer->normalize($clients, 'Json',['groups'=>'post:read']);
        return $this->render('admin_client/index.html.twig', [
            'controller_name' => 'AdminClientController',
            'clients'=>$clients,
            'data'=> $jsonContent,
        ]);
    }
 /**
     * @Route("/admin/client/Supp/{id}", name="d")
     */
    public function Delete($id)
    {
      
    $em= $this->getDoctrine()->getManager();
    $client =$em->getRepository(Client::class)->find($id);
    $em->remove($client);
    $em->flush();
    return  $this->redirectToRoute('app_admin_client');
    }

     /**
     * @Route("statclient", name="statclient")
     */
    public function indexAction(){
        $repository = $this->getDoctrine()->getRepository(Client::class);
        $client = $repository->findAll();
        $em = $this->getDoctrine()->getManager();
        
        $masc=0; 
        $fem=0;
       

        foreach ($client as $client)
        {
            if (  $client->getSexe()=="Masculin")  :
            
                $masc+=1; 
             else :
                $fem +=1;  
            
             endif;

        }


        $pieChart = new PieChart();
        $pieChart->getData()->setArrayToDataTable(
            [['Client', 'nombres'],
             ['Masculin',     $masc],
             ['Feminin',      $fem],
            ]
        );
        $pieChart->getOptions()->setTitle('STAT GENRE CLIENT');
        $pieChart->getOptions()->setHeight(500);
        $pieChart->getOptions()->setWidth(900);
        $pieChart->getOptions()->getTitleTextStyle()->setBold(true);
        $pieChart->getOptions()->getTitleTextStyle()->setColor('#009900');
        $pieChart->getOptions()->getTitleTextStyle()->setItalic(true);
        $pieChart->getOptions()->getTitleTextStyle()->setFontName('Arial');
        $pieChart->getOptions()->getTitleTextStyle()->setFontSize(20);
    
        return $this->render('client/stat.html.twig', array('piechart' => $pieChart));
        }

        /**
 * @Route("/pdf", name="pdfclient")
 */
    public function pdf()
    {
        // Configure Dompdf according to your needs
        $repository=$this->getDoctrine()->getRepository(Client::class); 
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Times New Roman');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        $client=$repository->findAll();
        //l'image est située au niveau du dossier public
        $png = file_get_contents("logo.png");
        $pngbase64 = base64_encode($png);
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('client/pdf.html.twig', [
             "img64"=>$pngbase64,
             'client'=>$client
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
        $dompdf->stream("client.pdf", [
            "Attachment" => false
        ]);
    }
}
