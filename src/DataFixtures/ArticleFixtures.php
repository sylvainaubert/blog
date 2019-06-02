<?php

namespace App\DataFixtures;

use App\Entity\Article;
use Faker;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class ArticleFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies()
    {
        return [CategoryFixtures::class];
    }

    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');

        for ($i = 0; $i < 50; $i++) {
            $article = new Article();
            $article->setTitle(mb_strtolower($faker->word()));
            $article->setContent(mb_strtolower($faker->sentence($nbWords = 6, $variableNbWords = true)));
            $manager->persist($article);
            $article->setCategory($this->getReference('categorie_' . rand(0,4)));
        }
        $manager->flush();
    }
}
