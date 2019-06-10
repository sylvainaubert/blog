<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Service\Slugify;
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
        $slugify = new Slugify();

        for ($i = 0; $i < 50; $i++) {
            $article = new Article();
            $article->setTitle(mb_strtolower($faker->realText($maxNbChars = 10, $indexSize = 2)));
            $article->setContent(mb_strtolower($faker->sentence($nbWords = 6, $variableNbWords = true)));
            $article->setPicture(preg_replace('/https/', 'http', $faker->imageUrl(480, 480, 'technics')));
            $article->setCategory($this->getReference('categorie_' . rand(0,4)));
            $article->setSlug($slugify->generate($article->getTitle()));
            $article->setAuthor($this->getReference('user_' . rand(0,1)));
            $manager->persist($article);
        }
        $manager->flush();
    }
}
