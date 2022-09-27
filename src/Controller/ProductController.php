<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route('/produits', name: 'products_index')]
    public function index(): Response
    {
        return $this->render('product/index.html.twig');
    }

    #[Route('/produits/{slug}', name: 'products_show')]
    public function show(): Response
    {
        return $this->render('product/show.html.twig');
    }
}
