<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use Faker\Factory;
use App\Entity\Image;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr-FR');

        

         for ($i = 1; $i <= 30 ; $i++){
             $ad = new Ad();

                $title = $faker->sentence();
                $coverImage = $faker->imageUrl(640, 480);
                $introduction = $faker->paragraph(2);
                $content = '<p>'. join('<p></p>', $faker->paragraphs(5)) .'</p>';
                $title = $faker->sentence();
                $title = $faker->sentence();

             $ad->setTitle($title)
                ->setCoverImage($coverImage)
                ->setIntroduction($introduction)
                ->setContent($content)
                ->setPrice(mt_rand(40, 200))
                ->setRooms(mt_rand(2, 6));

            for ($j = 1; $j <= mt_rand(2, 5); $j++){

                $image = new Image();

                $image->setUrl($faker->imageUrl())
                     ->setCaption($faker->sentence())
                     ->setAd($ad);

                $manager->persist($image);
                

            };  

            $manager->persist($ad);
         }
        $manager->flush();
    }
}
