<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Color;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    #[Route('/categorie/{slug}/{page}', name: 'category_index')]
    public function index(Category $category, $page): Response
    {
        // Manual pagination
        $products = $category->getProducts();
        $productsWithPagination = [];
        foreach ($products as $product) {
            array_push($productsWithPagination, $product);
        }
        $productsWithPagination = array_chunk($productsWithPagination, 6);
        
        $maxPage = count($productsWithPagination);
        $latestProduct = $this->manager->getRepository(Product::class)->findLatest(1)[0];
        $colors = $this->manager->getRepository(Color::class)->findAll();
        $categories = $this->manager->getRepository(Category::class)->findAll();

        return $this->render('category/index.html.twig', [
            'products' => $productsWithPagination[$page-1],
            'latestProduct' => $latestProduct,
            'page' => $page,
            'maxPage' => $maxPage,
            'colors' => $colors,
            'categories' => $categories,
            'category' => $category
        ]);
    }
}
