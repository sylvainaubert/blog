<?php


namespace App\Controller;

use App\Entity\Article;
use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class BlogController extends AbstractController
{

    /**
     * Show all row from article's entity
     *
     * @Route("/", name="index")
     * @return Response A response instance
     */
    public function index(): Response
    {
        $articles = $this->getDoctrine()
            ->getRepository(Article::class)
            ->findAll();

        if (!$articles) {
            throw $this->createNotFoundException(
                'No article found in article\'s table.'
            );
        }

        return $this->render(
            'blog/index.html.twig',
            ['articles' => $articles]
        );
    }
    /*
        /**
         * @return Response
         * @Route("/blog", name="blog_index")
         */
    /*   public function owner()
       {
           return $this->render('blog/index.html.twig', [
               'owner' => 'Sylvain',
           ]);
       }
   */
    /**
     * Getting a article with a formatted slug for title
     *
     * @param string $slug The slugger
     *
     * @Route("/blog/show/{slug<^[a-z0-9-]+$>}",
     *     defaults={"slug" = null},
     *     name="blog_show")
     * @return Response A response instance
     */
    public function show(?string $slug): Response
    {
        if (!$slug) {
            throw $this
                ->createNotFoundException('No slug has been sent to find an article in article\'s table.');
        }

        $slug = preg_replace(
            '/-/',
            ' ', ucwords(trim(strip_tags($slug)), "-")
        );

        $article = $this->getDoctrine()
            ->getRepository(Article::class)
            ->findOneBy(['id' => mb_strtolower($slug)]);

        if (!$article) {
            throw $this->createNotFoundException(
                'No article with ' . $slug . ' title, found in article\'s table.'
            );
        }

        return $this->render(
            'blog/show.html.twig',
            [
                'article' => $article,
                'slug' => $slug,
            ]
        );
    }

    /**
     * Getting a category
     *
     * @param string $category
     *
     * @Route("/blog/category/{category}",
     *     defaults={"category" = null},
     *     name="blog_category")
     * @return Response A response instance
     */
    public function showByCategory(string $category): response
    {
        if (!$category) {
            throw $this
                ->createNotFoundException('No category has been sent to find an article in article\'s table.');
        }

        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findOneByName($category);

        if (!$category) {
            throw $this->createNotFoundException(
                'No article with ' . $category . ' title, found in article\'s table.'
            );
        }

        $articles = $this->getDoctrine()
            ->getRepository(Article::class)
            ->findByCategory($category, ['id' => 'DESC'],3);

        return $this->render(
            'blog/category.html.twig',
            [
                'category' => $category,
                'articles' => $articles,
            ]
        );
    }
}
