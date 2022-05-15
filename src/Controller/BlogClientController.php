<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogClientController extends AbstractController
{
    /**
     * @Route("/blog/client", name="app_blog_client")
     */
    public function index(): Response
    {
        return $this->render('blog_client/index.html.twig', [
            'controller_name' => 'BlogClientController',
        ]);
    }
}
