<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Color;
use App\Entity\Product;
use App\Entity\Review;
use App\Form\ReviewType;
use App\Service\ProductsManager;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class ProductController extends AbstractController
{
    private $manager;
    private $security;

    public function __construct(EntityManagerInterface $manager, Security $security)
    {
        $this->manager = $manager;
        $this->security = $security;
    }

    #[Route('/produits/{page}', name: 'products_index', requirements: ['page' => '[0-9]'])]
    public function index($page, Request $request, RequestStack $requestStack, ProductsManager $productsManager): Response
    {
        $session = $requestStack->getSession();
        $products = $productsManager->getProductsFromProducts($request, $session, $page)['products'];
        $maxPage = $productsManager->getProductsFromProducts($request, $session, $page)['maxPage'];
        $latestProduct = $this->manager->getRepository(Product::class)->findLatest(1)[0];
        $colors = $this->manager->getRepository(Color::class)->findAll();
        $categories = $this->manager->getRepository(Category::class)->findAll();
        
        if ($session->get('color[]', null) !== null && ! empty($products)) {
            return $this->render('product/index.html.twig', [
                'products' => $products[$page - 1],
                'latestProduct' => $latestProduct,
                'page' => $page,
                'maxPage' => $maxPage,
                'colors' => $colors,
                'categories' => $categories,
            ]);
        } else {
            return $this->render('product/index.html.twig', [
                'products' => $products,
                'latestProduct' => $latestProduct,
                'page' => $page,
                'maxPage' => $maxPage,
                'colors' => $colors,
                'categories' => $categories,
            ]);
        }
    }

    #[Route('/produits/reset', name: 'products_reset')]
    public function reset(RequestStack $requestStack): Response
    {
        $requestStack->getSession()->remove('color[]');
        $requestStack->getSession()->remove('colorIds');

        return $this->redirectToRoute('products_index', [
            'page' => 1
        ]);
    }

    #[Route('/produits/{slug}', name: 'products_show')]
    public function show(Product $product, Request $request, RequestStack $requestStack): Response
    {
        // Handle review form
        $review = new Review();
        $reviewForm = $this->createForm(ReviewType::class, $review);
        $reviewForm->handleRequest($request);

        if ($reviewForm->isSubmitted() && $reviewForm->isValid()) {
            $review
                ->setProduct($product)
                ->setUser($this->security->getUser())
                ->setCreatedAt(DateTimeImmutable::createFromMutable(new DateTime()))
            ;

            $this->manager->persist($review);
            $this->manager->flush();
        }

        // Get the mean product rating
        $productReviews = $product->getReviews();
        $totalRating = 0;
        foreach ($productReviews as $review) {
            $totalRating += $review->getRating();
        }
        if (! empty($productReviews->toArray())) {
            $meanRating = ($totalRating / count($productReviews->toArray()));
        }

        // Add to cart
        if ($request->request->get('color') && $request->request->get('quantity')) {
            $session = $requestStack->getSession();
            $productsInCart = $session->get('cart', []);
            array_push(
                $productsInCart,
                [
                    'product' => $product,
                    'color' => $request->request->get('color'),
                    'quantity' => $request->request->get('quantity')
                ]
            );
            $session->set('cart', $productsInCart);

            return $this->redirectToRoute('cart_index');
        }

        return $this->render('product/show.html.twig', [
            'product' => $product,
            'productReviews' => $productReviews,
            'meanRating' => $meanRating ?? null,
            'reviewForm' => $reviewForm->createView()
        ]);
    }
}
