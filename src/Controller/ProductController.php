<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Color;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    #[Route('/produits/{page}', name: 'products_index', requirements: ['page' => '[0-9]'])]
    public function index($page): Response
    {
        $products = $this->manager->getRepository(Product::class)->getProducts($page);
        $maxPage = (int) ceil(count($products) / 6);
        $latestProduct = $this->manager->getRepository(Product::class)->findLatest(1)[0];
        $colors = $this->manager->getRepository(Color::class)->findAll();
        $categories = $this->manager->getRepository(Category::class)->findAll();

        return $this->render('product/index.html.twig', [
            'products' => $products,
            'latestProduct' => $latestProduct,
            'page' => $page,
            'maxPage' => $maxPage,
            'colors' => $colors,
            'categories' => $categories
        ]);
    }

    #[Route('/produits/{slug}', name: 'products_show')]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product
        ]);
    }
}
