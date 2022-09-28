<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DomCrawler\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\AsciiSlugger;

class AdminController extends AbstractController
{
    private $doctrine;
    private $manager;

    public function __construct(ManagerRegistry $doctrine, EntityManagerInterface $manager)
    {
        $this->doctrine = $doctrine;
        $this->manager = $manager;
    }

    #[Route('/admin', name: 'admin_index')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }

    #[Route('/admin/produits', name: 'admin_list')]
    public function list(): Response
    {
        $products = $this->manager->getRepository(Product::class)->findAll();

        return $this->render('admin/list.html.twig', [
            'products' => $products
        ]);
    }

    #[Route('/admin/produits/nouveau', name: 'admin_create')]
    public function create(Request $request): Response
    {
        $product = new Product();
        $creationForm = $this->createForm(ProductType::class, $product);

        $creationForm->handleRequest($request);

        if ($creationForm->isSubmitted() && $creationForm->isValid()) {
            // dd($request, $request->get('product')['name']);
            $product->setSlug((new AsciiSlugger())->slug($request->get('product')['name'])->lower());
            $product->setCreatedAt(DateTimeImmutable::createFromMutable(new DateTime()));

            $this->manager->persist($product);
            $this->manager->flush();

            return $this->redirectToRoute('admin_list');
        }

        return $this->render('admin/create.html.twig', [
            'creationForm' => $creationForm->createView()
        ]);
    }
}
