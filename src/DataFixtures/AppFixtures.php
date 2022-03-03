<?php

namespace App\DataFixtures;


use Faker\Factory;
use App\Entity\User;
use App\Entity\Product;
use App\Entity\Allergen;
use App\Entity\Category;
use Bluemmb\Faker\PicsumPhotosProvider;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    protected $faker;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {

        for ($i = 0; $i < 5; $i++) {
            $this->faker = Factory::create('fr_FR');
            $category = new Category;
            $category->setName($this->faker->name())
                ->setDescription($this->faker->sentence());
            $manager->persist($category);
        }

        for ($i = 0; $i < 20; $i++) {
            $this->faker = Factory::create('fr_FR');
            $this->faker->addProvider(new PicsumPhotosProvider($this->faker));
            $product = new Product();
            $product->setName($this->faker->name())
                ->setImage($this->faker->imageUrl(500, 500, true))
                ->setDescription($this->faker->sentence())
                ->setWeight($this->faker->randomDigitNotNull())
                ->setPrice($this->faker->randomNumber(3, false))
                ->setStock($this->faker->randomNumber(4, false))
                ->setCategory($category);
            $manager->persist($product);
        }

        for ($i = 0; $i < 10; $i++) {
            $this->faker = Factory::create('fr_FR');
            $allergen = new Allergen();
            $allergen->setName($this->faker->name())
                ->setDescription($this->faker->sentence());
            $manager->persist($allergen);
        }


        $user = new User();
        $password = $this->hasher->hashPassword($user, 'adminadmin');
        $user->setEmail("quang@mail.com")
            ->setRoles(["ROLE_ADMIN"])
            ->setPassword($password)
            ->setFirstname("Prénom")
            ->setLastname("Nom")
            ->setAddress("Gaillac")
            ->setPhone("0000000000")
            ->setCreatedAt(new \DateTimeImmutable)
            ->setIsVerified(1);
        $manager->persist($user);

        $manager->flush();
    }
}
