<?php

namespace App\Controller;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\Employe;
use App\Form\EmployeType;
use App\Repository\EmployeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\PieChart;
use Knp\Component\Pager\PaginatorInterface;



class EmployeController extends AbstractController
{


    /**
     * @Route("/employe", name="display_Employe")
     */
    public function index(EmployeRepository $rep): Response
    {

        $employes = $this->getDoctrine()->getManager()->getRepository(Employe::class)->findAll();
        $table =$this->stats2($rep);

        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery(
            'SELECT sum(ev.salaireemploye*12) FROM App\Entity\Employe ev'
        );

        $eq = $query->getSingleScalarResult();

        return $this->render('employe/index.html.twig', [
            'E'=>$employes,
            "table"=>$table,
            'chiffre' => $eq


        ]);
    }

    /**
     * @Route("/admin", name="display_admin")
     */
    public function indexAdmin(): Response
    {

        return $this->render('Admin/index.html.twig'
        );
    }


    /**
     * @Route("/addEmploye", name="addEmploye")
     */
    public function addEmploye(Request $request): Response
    {
        $employe = new Employe();

        $form = $this->createForm(EmployeType::class,$employe);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($employe);//Add
            $em->flush();

            $this->addFlash(
                'info',
                'employé ajoutée avec succés');

            return $this->redirectToRoute('display_Employe');
        }
        return $this->render('Employe/createemploye.html.twig',['f'=>$form->createView()]);




    }

    /**
     * @Route("/removeEmploye/{id}", name="supp_employe")
     */
    public function suppressionEmploye(Employe  $employe): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($employe);
        $em->flush();

        return $this->redirectToRoute('display_Employe');


    }
    /**
     * @Route("/modifEmploye/{id}", name="modifemploye")
     */
    public function modifEmploye(Request $request,$id): Response
    {
        $employe = $this->getDoctrine()->getManager()->getRepository(employe::class)->find($id);

        $form = $this->createForm(EmployeType::class,$employe);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute('display_Employe');
        }
        return $this->render('employe/updateEmploye.html.twig',['f'=>$form->createView()]);




    }




    /**
     * @Route("/Liste", name="Liste")
     */
    public function pdf()
    {
        // Configure Dompdf according to your needs
        $repository=$this->getDoctrine()->getRepository(Employe::class);

        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options

        $dompdf = new Dompdf($pdfOptions);
        $employeee=$repository->findall();

        //l'image est située au niveau du dossier public
        $png = file_get_contents("l.png");
        $pngbase64 = base64_encode($png);
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('employe/liste.html.twig', [
            "img64"=>$pngbase64,"employe"=>$employeee
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
     * @Route("/stats", name="stats")
     */
    public function new(): Response
    { $repository = $this->getDoctrine()->getRepository(Employe::class);
        $employe = $repository->findAll();
        $em = $this->getDoctrine()->getManager();

        $rd=0;
        $qu=0;
        $es=0;


        foreach ($employe as $employe)
        {
            if (  $employe->getSalaireemploye()<1000)  :

                $rd+=1;
            elseif ($employe->getSalaireemploye()>1000):

                $qu+=1;


            endif;

        }


        $pieChart = new PieChart();
        $pieChart->getData()->setArrayToDataTable(
            [['salaireemploye', 'nombres'],
                ['<1000',     $rd],
                ['>1000',      $qu]

            ]
        );
        $pieChart->getOptions()->setColors(['#ffd700', '#C0C0C0', '#cd7f32']);

        $pieChart->getOptions()->setHeight(500);
        $pieChart->getOptions()->setWidth(900);
        $pieChart->getOptions()->getTitleTextStyle()->setBold(true);
        $pieChart->getOptions()->getTitleTextStyle()->setColor('#009900');
        $pieChart->getOptions()->getTitleTextStyle()->setItalic(true);
        $pieChart->getOptions()->getTitleTextStyle()->setFontName('Arial');
        $pieChart->getOptions()->getTitleTextStyle()->setFontSize(20);

        return $this->render('employe/stat.html.twig', array('piechart' => $pieChart,"E"=>$employe));




    }







    /**
     * @Route("trinom", name="trinom")
     */
    public function triNom(Request $request, PaginatorInterface $paginator,EmployeRepository $rep)
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery(
            'SELECT ev FROM App\Entity\Employe ev 
            ORDER BY ev.nomemploye'
        );

        $eq = $query->getResult();
        $eq = $paginator->paginate(
            $eq,
            $request->query->getInt('page',1),
            4
        );
        $em1 = $this->getDoctrine()->getManager();

        $query1 = $em1->createQuery(
            'SELECT sum(ev.salaireemploye*12) FROM App\Entity\Employe ev'
        );


        $table =$this->stats2($rep);

        $em2 = $this->getDoctrine()->getManager();

        $query2 = $em->createQuery(
            'SELECT sum(ev.salaireemploye*12) FROM App\Entity\Employe ev'
        );






        $eq1 = $query1->getSingleScalarResult();
        return $this->render('employe/index.html.twig', [
            'E'=>$eq,
            "table"=>$table,
            'chiffre' => $eq1


        ]);
    }


    /**
     * @Route("trisalaire", name="trisalaire")
     */
    public function trisalaire(Request $request, PaginatorInterface $paginator,EmployeRepository $rep)
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery(
            'SELECT ev FROM App\Entity\Employe ev 
            ORDER BY ev.salaireemploye'
        );

        $eq = $query->getResult();
        $eq = $paginator->paginate(
            $eq,
            $request->query->getInt('page',1),
            4
        );
        $em1 = $this->getDoctrine()->getManager();

        $query1 = $em1->createQuery(
            'SELECT sum(ev.salaireemploye*12) FROM App\Entity\Employe ev'
        );


        $table =$this->stats2($rep);

        $em2 = $this->getDoctrine()->getManager();

        $query2 = $em->createQuery(
            'SELECT sum(ev.salaireemploye*12) FROM App\Entity\Employe ev'
        );






        $eq1 = $query1->getSingleScalarResult();
        return $this->render('employe/index.html.twig', [
            'E'=>$eq,
            "table"=>$table,
            'chiffre' => $eq1


        ]);
    }










    public function stats2(EmployeRepository $repCat)
    {
        $categories = $repCat->findAll();


        $catNom =[0,0];








        foreach ($categories as $cat) {
            if (  $cat->getSalaireemploye()<1000)  :

                $catNom[0]+=1;
            elseif ($cat->getSalaireemploye()>1000):

                $catNom[1]+=1;


            endif;





        }


        return  json_encode($catNom);

    }





}
