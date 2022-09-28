<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Color;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    private $doctrine;
    private $manager;

    public function __construct(ManagerRegistry $doctrine, EntityManagerInterface $manager)
    {
        $this->doctrine = $doctrine;
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
        $latestProduct = $this->doctrine->getRepository(Product::class)->findLatest(1)[0];
        $colors = $this->doctrine->getRepository(Color::class)->findAll();
        $categories = $this->doctrine->getRepository(Category::class)->findAll();

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
