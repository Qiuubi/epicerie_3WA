<?php

namespace App\DataFixtures;


use Faker\Factory;
use App\Entity\Product;
use App\Entity\Category;
use Bluemmb\Faker\PicsumPhotosProvider;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    protected $faker;

    public function load(ObjectManager $manager): void
    {

        for ($i = 0; $i < 20; $i++) {
            $this->faker = Factory::create('fr_FR');
            $category = new Category;
            $category->setName($this->faker->name())
                ->setDescription($this->faker->paragraph());
        }

        for ($i = 0; $i < 20; $i++) {
            $this->faker = Factory::create('fr_FR');
            $this->faker->addProvider(new PicsumPhotosProvider($this->faker));
            $product = new Product();
            $product->setName($this->faker->name())
                ->setImage($this->faker->imageUrl(500, 500, true))
                ->setDescription($this->faker->paragraph())
                ->setWeight($this->faker->randomDigitNotNull())
                ->setPrice($this->faker->randomNumber(3, false))
                ->setStock($this->faker->randomNumber(4, false))
                ->setCategory($category);
            $manager->persist($product);
        }


        $manager->flush();
    }
}
