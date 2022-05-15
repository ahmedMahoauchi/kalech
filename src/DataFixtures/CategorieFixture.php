<?php

namespace App\DataFixtures;

use App\Entity\CategorieProduit;
use App\Entity\ProduitShop;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class CategorieFixture extends AppFixtures
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create();
        $productArray = array("566f8f5c8ef4eec1207a1558047a4c6c.jpeg", "ffa6995c3971f61b00a30e3a41619491.jpeg","hoodie 5.jpg", "hoodie 3.jpg","hoodie 4.jpg");

        $i = 0;
       while ($i <= 5) { 
            $categorie = new CategorieProduit();
            $categorie->setNomCategorie($faker->sentence());
            $categorie->setDescriptionCategorie($faker->paragraph());

            $manager->persist($categorie);
            $i++;

            for($j = 0 ; $j<=6 ; $j++){
                $produit = new ProduitShop();
                $produit->setNom($faker->sentence());
                $produit->setDescription($faker->paragraphs(3, true));
                $produit->setQt($faker->numberBetween(0, 100));
                $produit->setPrix($faker->randomFloat(1, 20, 30));
                $key =array_rand($productArray);
                $produit->setImage($productArray[$key]);
                
                $produit->setCategorie($categorie);
                
                $manager->persist($produit);
            }

            
    }

        $manager->flush();
    }
}
