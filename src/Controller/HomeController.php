<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private $manager;
    private $products;
    private $mailer;

    public function __construct(EntityManagerInterface $manager, MailerInterface $mailer)
    {
        $this->manager = $manager;
        $this->products = $manager->getRepository(Product::class)->findAll();
        $this->mailer = $mailer;
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
        $latestProducts = $this->manager->getRepository(Product::class)->findLatest(4);

        // Best products
        $bestProducts = $this->manager->getRepository(Product::class)->findBest(4);
        
        return $this->render('home/index.html.twig', [
            'sliderProducts' => $sliderProducts,
            'favorite' => $favoriteProduct,
            'latestProducts' => $latestProducts,
            'bestProducts' => $bestProducts
        ]);
    }

    #[Route('/contact', name: 'home_contact')]
    public function contact(Request $request): Response
    {
        $contactForm = $this->createForm(ContactType::class);
        $contactForm->handleRequest($request);

        if ($contactForm->isSubmitted() && $contactForm->isValid()) {
            $formInfo = $request->request->all()['contact'];

            $mail = (new TemplatedEmail())
                ->from($formInfo['email'])
                ->to('myAmazingCompanyInSiliconValley@admin.com')
                ->subject('Contact form - '.$formInfo['subject'])
                ->htmlTemplate('email/contact.html.twig')
                ->context([
                    'name' => $formInfo['name'],
                    'message' => $formInfo['message']
                ])
            ;

            $this->mailer->send($mail);
            $this->addFlash('confirmation', 'Votre demande a bien été prise en compte.');
        }

        return $this->render('home/contact.html.twig', [
            'contactForm' => $contactForm->createView()
        ]);
    }
}
