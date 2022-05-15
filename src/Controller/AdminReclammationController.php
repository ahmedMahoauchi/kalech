<?php

namespace App\Controller;
use App\Entity\Reclammation;
use App\Repository\ReclammationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\PieChart;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class AdminReclammationController extends AbstractController
{
    /**
     * @Route("/admin/reclammation", name="app_admin_reclammation")
     */
    public function index(ReclammationRepository $repo, NormalizerInterface $Normalizer): Response
    {
        $reclas = $repo->findAll();
        $jsonContent = $Normalizer->normalize($reclas, 'Json',['groups'=>'post:read']);
        return $this->render('admin_reclammation/index.html.twig', [
            'controller_name' => 'AdminReclammationController',
            'reclas'=>$reclas,
            'data'=> $jsonContent,
        ]);
    }

    /**
     * @Route("/admin/reclammation/supp/{id}", name="dd")
     */
    public function Delete($id)
{
  


$em= $this->getDoctrine()->getManager();
$recla =$em->getRepository(Reclammation::class)->find($id);
$em->remove($recla);
$em->flush();
return  $this->redirectToRoute('app_admin_reclammation');
}

 /**
     * @Route("statrecla", name="statrecla")
     */
    public function indexAction(){
        $repository = $this->getDoctrine()->getRepository(Reclammation::class);
        $recla = $repository->findAll();
        $em = $this->getDoctrine()->getManager();
        
        $c=0; 
        $m=0;
        $p=0;
       

        foreach ($recla as $recla)
        {
            if (  $recla->getTheme()=="Machine")  :
            
                $m+=1;
            elseif (  $recla->getTheme()=="Coach")  :
            
                    $c+=1; 
             else :
                $p +=1;  
            
             endif;

        }


        $pieChart = new PieChart();
        $pieChart->getData()->setArrayToDataTable(
            [['Reclammation', 'nombres'],
             ['Machine',     $m],
            ['Coach',      $c],
            ['Programme',      $p],

            ]
        );
        $pieChart->getOptions()->setTitle('STAT THEME RECLAMMATION');
        $pieChart->getOptions()->setHeight(500);
        $pieChart->getOptions()->setWidth(900);
        $pieChart->getOptions()->getTitleTextStyle()->setBold(true);
        $pieChart->getOptions()->getTitleTextStyle()->setColor('#009900');
        $pieChart->getOptions()->getTitleTextStyle()->setItalic(true);
        $pieChart->getOptions()->getTitleTextStyle()->setFontName('Arial');
        $pieChart->getOptions()->getTitleTextStyle()->setFontSize(20);
    
        return $this->render('reclammation/stat.html.twig', array('piechart' => $pieChart));
        }
}
