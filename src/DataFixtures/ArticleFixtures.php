<?php

namespace App\DataFixtures;

use App\Entity\Article;
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

        $article = new Article();
        $article->setTitle('Framework PHP : Symfony 4');
        $article->setContent('Symfony 4 c\'est coolos');
        $manager->persist($article);
        $article->setCategory($this->getReference('categorie_0'));
        $manager->flush();
    }
}
