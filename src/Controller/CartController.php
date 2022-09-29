<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    private $session;

    public function __construct(RequestStack $requestStack)
    {
        $this->session = $requestStack->getSession();
    }

    #[Route('/panier', name: 'cart_index')]
    public function index(): Response
    {
        $cart = $this->session->get('cart') ?? [];
        $subtotal = 0;
        foreach ($cart as $productInfo) {
            if ($productInfo['product']->getPromotion()) {
                $subtotal += (int) ($productInfo['product']->getPrice() * (1 - $productInfo['product']->getPromotion()/100) * (int) $productInfo['quantity']); 
            } else {
                $subtotal += (int) ($productInfo['product']->getPrice() * (int) $productInfo['quantity']); 
            }
        }
        if ($subtotal > 10000) $transportCost = 0;
        else $transportCost = 690;
        $total = $subtotal + $transportCost; 
        
        return $this->render('cart/index.html.twig', [
            'cart' => $cart,
            'transportCost' => $transportCost,
            'subtotal' => $subtotal,
            'total' => $total
        ]);
    }

    #[Route('/panier/{id}/supprimer', name: 'cart_delete')]
    public function delete($id): Response
    {
        $cart = $this->session->get('cart');
        unset($cart[$id]);
        $cart = array_values($cart);
        
        $this->session->set('cart', $cart);

        return $this->redirectToRoute('cart_index');
    }
}
