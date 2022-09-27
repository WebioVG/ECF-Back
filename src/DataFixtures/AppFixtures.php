<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Color;
use App\Entity\Product;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\String\Slugger\AsciiSlugger;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $faker->addProvider(new \DavidBadura\FakerMarkdownGenerator\FakerProvider($faker));

        // Create 10 colors and store them
        $colors = [];
        for ($i = 0; $i < 10; $i++) { 
            $color = new Color();
            $color
                ->setName($faker->colorName())
                ->setHex($faker->hexColor())
            ;
            array_push($colors, $color);
            $manager->persist($color);
        }

        // Create 10 categories and 3 to 8 products per category
        for ($j = 0; $j < 10; $j++) { 
            $category = new Category();
            $category
                ->setName($categoryName = $faker->words(rand(1, 3), true))
                ->setSlug((new AsciiSlugger())->slug($categoryName)->lower())
            ;
            $manager->persist($category);

            for ($k = 0; $k < rand(3, 8); $k++) {
                $product = new Product();

                $product
                    ->setName($productName = $faker->sentence(rand(1,3)))
                    ->setDescription($faker->markdown())
                    ->setPrice(rand(10, 2000) * 100)
                    ->setSlug((new AsciiSlugger())->slug($productName)->lower())
                    ->setCreatedAt(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-5 years')))
                    ->setFavorite(rand(0, 1))
                    ->setImage($faker->imageUrl())
                    ->setPromotion(rand(0, 1) === 0 ? rand(0, 50) : null)
                    ->setCategory($category)
                ;

                // Add prudct colors
                $productColors = [];

                for ($l = 0; $l < rand(1, 5); $l++) {
                    $newColor =  $colors[rand(0, count($colors) - 1)];
                    if (! in_array($newColor, $productColors)) {
                        array_push($productColors, $newColor);
                    }
                }

                foreach ($productColors as $color) {
                    $product
                        ->addColor($color)
                    ;
                }

                $manager->persist($product);
            }
        }

        $manager->flush();
    }
}
