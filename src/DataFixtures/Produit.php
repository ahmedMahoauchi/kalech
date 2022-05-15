<?php

namespace App\DataFixtures;

use App\Entity\CategorieProduit;
use App\Entity\Produit as EntityProduit;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class Produit extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create();

        //creer 3 categories fakés
        for ($i=0; $i < 12 ; $i++) { 
            $categorie = new CategorieProduit();
            $categorie->setNomCategorie($faker->sentence())
                      ->setDescriptionCategorie($faker->paragraph());
            $manager->persist($categorie);

            for($i = 0 ; $i<=15 ; $i++){
                $produit = new EntityProduit();
                $produit->setNomProduit($faker->sentence());
                $produit->setDescriptionProduit($faker->paragraph());
                $produit->setStock($faker->numberBetween(0, 100));
                $produit->setPrix($faker->randomFloat(1, 20, 30));
                $produit->setImage($faker->imageUrl(450, 300, 'animals', true));
                $produit->setCategorie($categorie);
                
                $manager->persist($categorie);
            }

    }

        $manager->flush();
}
}
