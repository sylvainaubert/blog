<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Tag;
use App\Entity\Article;
use App\Service\Slugify;
use Faker;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @var Slugify
     */
    private $slugify;

    /**
     * AppFixtures constructor.
     * @param Slugify $slugify
     */
    public function __construct(Slugify $slugify)
    {
        $this->slugify = $slugify;
    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on
     *
     * @return array
     */
    public function getDependencies()
    {
        return [UserFixtures::class, CategoryFixtures::class];
    }

    public function load(ObjectManager $manager)
    {
       /* $faker = Faker\Factory::create('fr_FR');
        $slugify = new Slugify();

        for ($i = 1; $i <= 1000; $i++) {
            $category = new Category();
            $category->setName($i);
            $manager->persist($category);

            $tag = new Tag();
            $tag->setName('tag_' . rand(0,3));
            $manager->persist($tag);

            $article = new Article();
            $article->setTitle(mb_strtolower($faker->realText($maxNbChars = 10, $indexSize = 2)));
            $article->setContent(mb_strtolower($faker->sentence($nbWords = 6, $variableNbWords = true)));
            $article->setPicture(preg_replace('/https/', 'http', $faker->imageUrl(480, 480, 'technics')));
            $article->setSlug($slugify->generate($article->getTitle()));
            $article->setCategory($category);
            $article->setAuthor($this->getReference('user_' . rand(0,1)));
            $article->addTag($tag);
            $manager->persist($article);
        }
*/
        $manager->flush();
    }
}
