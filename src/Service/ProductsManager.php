<?php

namespace App\Service;

use App\Entity\Color;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;

class ProductsManager
{
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function getProductsFromProducts($request, $currentSession, $page)
    {
        $session = $currentSession;

        if (in_array('color', array_keys($request->request->all()))) {
            $products = [];
            $filteredProductsIds = [];
            $filters = [];

            foreach ($request->request->all()['color'] as $key => $colorId) {
                array_push($filters, $colorId);
                ${'color'.$key} = $this->manager->getRepository(Color::class)->find($colorId);

                foreach (${'color'.$key}->getProducts()->toArray() as $product) {
                    if (! in_array($product->getId(), $filteredProductsIds)) {
                        array_push($filteredProductsIds, $product->getId());
                        array_push($products, $product);
                    }
                }
            }

            $session->set('color[]', $products);
            $session->set('colorIds', $filters);
        }

        // Get products or color-filtered products
        if ($session->get('color[]', null) !== null) {
            $products = $session->get('color[]');
            
            // Manual pagination
            $products = array_chunk($products, 6);
            $maxPage = count($products);
        } else {
            $products = $this->manager->getRepository(Product::class)->getProducts($page);
            $maxPage = (int) ceil(count($products) / 6);
        }

        return [
            'products' => $products,
            'maxPage' => $maxPage
        ];
    }

    public function getProductsFromCategory($request, $currentSession, $products, $category)
    {
        $filters = []; // to check the active colors checkboxes in the view
        $session = $currentSession;

        if (in_array('color', array_keys($request->request->all()))) {
            $filteredProducts = [];
            $filteredProductsIds = [];

            foreach ($request->request->all()['color'] as $colorId) {
                array_push($filters, $colorId);

                foreach ($products as $product) {
                    foreach ($product->getColors() as $color) {
                        if ($color->getId() === (int) $colorId && ! in_array( $product->getId(), $filteredProductsIds)) {
                            array_push($filteredProductsIds, $product->getId());
                            array_push($filteredProducts, $product);
                        }
                    }
                }
            }

            $products = $filteredProducts;
            $session->set('color[]', $products);
            $session->set('colorIds', $filters);
        } elseif ($session->get('color[]', null) !== null) {
            $products = [];
            foreach ($session->get('color[]') as $product) {
                if ($product->getCategory()->getId() === $category->getId()) {
                    array_push($products, $product);
                }
            }
        }

        return $products;
    }
}