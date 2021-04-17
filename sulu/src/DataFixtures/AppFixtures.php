<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $suluBookProduct = new Product();
        $suluBookProduct->setName('Sulu 2: The Fast Track Book');
        $suluBookProduct->setPrice(30);
        $manager->persist($suluBookProduct);

        $symfonyBookProduct = new Product();
        $symfonyBookProduct->setName('Symfony 5: The Fast Track Book');
        $symfonyBookProduct->setPrice(35);
        $manager->persist($symfonyBookProduct);

        $bibleBookProduct = new Product();
        $bibleBookProduct->setName('The Holy Bible');
        $bibleBookProduct->setPrice(0);
        $manager->persist($bibleBookProduct);

        $manager->flush();
    }
}
