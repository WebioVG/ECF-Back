<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Review;
use App\Form\ProductType;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\AsciiSlugger;

class AdminController extends AbstractController
{
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    #[Route('/admin', name: 'admin_index')]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }

    #[Route('/admin/produits', name: 'admin_products')]
    public function listProducts(): Response
    {
        $products = $this->manager->getRepository(Product::class)->findAll();

        return $this->render('admin/products.html.twig', [
            'products' => $products
        ]);
    }

    #[Route('/admin/avis', name: 'admin_reviews')]
    public function listReviews(): Response
    {
        $reviews = $this->manager->getRepository(Review::class)->findAll();

        return $this->render('admin/reviews.html.twig', [
            'reviews' => $reviews
        ]);
    }

    #[Route('/admin/produits/nouveau', name: 'admin_create')]
    #[Route('/admin/produits/{id}/modifier', name: 'admin_edit')]
    #[IsGranted('ROLE_USER')]
    public function createOrEdit(Request $request, Product $product = null): Response
    {
        if (! $product) {
            $product = new Product();
        }
        $creationForm = $this->createForm(ProductType::class, $product);

        $creationForm->handleRequest($request);

        if ($creationForm->isSubmitted() && $creationForm->isValid()) {
            $product->setSlug((new AsciiSlugger())->slug($request->request->all()['product']['name'])->lower());
            if (! $product->getId()) {
                $product->setCreatedAt(DateTimeImmutable::createFromMutable(new DateTime()));
            }

            $this->manager->persist($product);
            $this->manager->flush();

            return $this->redirectToRoute('admin_products');
        }

        return $this->render('admin/create.html.twig', [
            'creationForm' => $creationForm->createView(),
            'editMode' => $product->getId() !== null,
            'product' => $product
        ]);
    }

    #[Route('admin/produits/{id}/supprimer', name: 'admin_delete_product')]
    #[IsGranted('ROLE_USER')]
    public function delete(Product $product)
    {
        $this->manager->remove($product);
        $this->manager->flush();

        return $this->redirectToRoute('admin_products');
    }

    #[Route('admin/avis/{id}/supprimer', name: 'admin_delete_review')]
    #[IsGranted('ROLE_USER')]
    public function deleteReview(Review $review)
    {
        $this->manager->remove($review);
        $this->manager->flush();

        return $this->redirectToRoute('admin_reviews');
    }
}
