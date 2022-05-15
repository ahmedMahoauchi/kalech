<?php

namespace App\Controller;

use App\Entity\Employe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Annotation\Groups;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Validator\Constraints\Json;


class EmployeeController extends AbstractController
{

    /******************affichage employe*****************************************/











/**
 * @Route("/displayEmployes", name="display_employe")
 */

public function allEmploye(NormalizerInterface $Normalizer )
{
    //Nous utilisons la Repository pour récupérer les objets que nous avons dans la base de données
    $repository =$this->getDoctrine()->getRepository(Employe::class);
    $Event=$repository->FindAll();
    //Nous utilisons la fonction normalize qui transforme en format JSON nos donnée qui sont
    //en tableau d'objet Students
    $jsonContent=$Normalizer->normalize($Event,'json',['groups'=>'post:read']);



    return new Response(json_encode($jsonContent));
    dump($jsonContent);
    die;}




    /******************Ajouter Reclamation*****************************************/
    /**
     * @Route("/addEmployee", name="add_employeez")
     * @Method("POST")
     */

    public function ajouteremployeAction(Request $request)
    {
        $employe = new Employe();
        $nomemploye = $request->query->get("nom");
        $dateemploye = $request->query->get("date");
        $numemploye = $request->query->get("num");
        $salaireemploye = $request->query->get("salaire");


        $em = $this->getDoctrine()->getManager();

        $employe->setNomemploye($nomemploye);
        $employe->setDateemploye($dateemploye);


        $employe->setNumemploye($numemploye);
        $employe->setSalaireemploye($salaireemploye);

        $em->persist($employe);
        $em->flush();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($employe);
        return new JsonResponse($formatted);

    }

    /******************Supprimer Employe*****************************************/

    /**
     * @Route("/deleteEmployee", name="delete_employe")
     * @Method("DELETE")
     */

    public function deleteReclamationAction(Request $request) {
        $id = $request->get("idemploye");

        $em = $this->getDoctrine()->getManager();
        $reclamation = $em->getRepository(employe::class)->find($id);
        if($reclamation!=null ) {
            $em->remove($reclamation);
            $em->flush();

            $serialize = new Serializer([new ObjectNormalizer()]);
            $formatted = $serialize->normalize("employe a ete supprimee avec success.");
            return new JsonResponse($formatted);

        }
        return new JsonResponse("id employe invalide.");


    }

    /******************Modifier Employe****************************************/
    /**
     * @Route("/updateEmployee", name="update_employe")
     * @Method("PUT")
     */
    public function modifierEmployeAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $employe = $this->getDoctrine()->getManager()
            ->getRepository(Employe::class)
            ->find($request->get("id"));

        $employe->setNomemploye($request->get("nomemploye"));
        $employe->setDateEmploye($request->get("dateemploye"));

        $em->persist($employe);
        $em->flush();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($employe);
        return new JsonResponse("employe a ete modifiee avec success.");

    }






}
