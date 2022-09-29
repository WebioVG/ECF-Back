<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Color;
use App\Entity\Product;
use App\Service\ProductsManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    #[Route('/categorie/{slug}/{page}', name: 'category_index', requirements: ['page' => '[0-9]'])]
    public function index(Category $category, $page, Request $request, ProductsManager $productsManager, RequestStack $requestStack): Response
    {
        $products = $productsManager->getProductsFromCategory($request, $requestStack->getSession(), $category->getProducts(), $category);

        // Manual pagination
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
            'products' => empty($productsWithPagination) ? [] : $productsWithPagination[$page-1],
            'latestProduct' => $latestProduct,
            'page' => $page,
            'maxPage' => $maxPage,
            'colors' => $colors,
            'categories' => $categories,
            'category' => $category
        ]);
    }

    #[Route('/categorie/{slug}/reset', name: 'category_reset')]
    public function resetFilter(RequestStack $requestStack, $slug)
    {
        $requestStack->getSession()->remove('color[]');
        $requestStack->getSession()->remove('colorIds');

        return $this->redirectToRoute('category_index', [
            'slug' => $slug,
            'page' => 1
        ]);
    }
}
