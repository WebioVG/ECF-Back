<?php

namespace App\Controller;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private $doctrine;
    private $manager;
    private $products;

    public function __construct(PersistenceManagerRegistry $doctrine, EntityManagerInterface $manager)
    {
        $this->doctrine = $doctrine;
        $this->manager = $manager;
        $this->products = $doctrine->getRepository(Product::class)->findAll();
    }

    #[Route('/', name: 'home_index')]
    public function index(): Response
    {
        // Slider products
        $sliderProducts = [];

        $randoms = range(0, count($this->products) - 1);
        shuffle($randoms);
        $randoms = array_slice($randoms, 0, 3);
        
        foreach ($randoms as $random) {
            array_push($sliderProducts, $this->products[$random]);
        }

        // Favorite product
        $favoriteProduct = $this->products[rand(0, count($this->products) - 1)];

        // Latest products
        $latestProducts = $this->doctrine->getRepository(Product::class)->findLatest(4);
        dump($this->doctrine->getRepository(Product::class));
        
        return $this->render('home/index.html.twig', [
            'sliderProducts' => $sliderProducts,
            'favorite' => $favoriteProduct,
            'latestProducts' => $latestProducts
        ]);
    }

    #[Route('/contact', name: 'home_contact')]
    public function contact(): Response
    {
        return $this->render('home/contact.html.twig');
    }
}
