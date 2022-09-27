<?php

namespace App\Controller;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

class ProductController extends AbstractController
{
    private $doctrine;
    private $manager;

    public function __construct(ManagerRegistry $doctrine, EntityManagerInterface $manager)
    {
        $this->doctrine = $doctrine;
        $this->manager = $manager;
    }

    #[Route('/produits/{page}', name: 'products_index', requirements: ['page' => '[0-9]'])]
    public function index($page): Response
    {
        $products = $this->doctrine->getRepository(Product::class)->getProducts($page);
        $maxPage = (int) ceil(count($products) / 6);
        $latestProduct = $this->doctrine->getRepository(Product::class)->findLatest(1)[0];

        return $this->render('product/index.html.twig', [
            'products' => $products,
            'latestProduct' => $latestProduct,
            'page' => $page,
            'maxPage' => $maxPage
        ]);
    }

    #[Route('/produits/{slug}', name: 'products_show')]
    public function show(): Response
    {
        return $this->render('product/show.html.twig');
    }
}
