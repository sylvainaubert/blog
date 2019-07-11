<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use App\Service\Slugify;
use Doctrine\Common\Persistence\ObjectManager;
use function PHPSTORM_META\type;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/article")
 */
class ArticleController extends AbstractController
{
    /**
     * @Route("/", name="article_index", methods={"GET"})
     * @param ArticleRepository $articleRepository
     * @return Response
     */
    public function index(ArticleRepository $articleRepository): Response
    {

        return $this->render('article/index.html.twig', [
            'articles' => $articleRepository->findAllWithCategoriesAndTags()
        ]);
    }

    /**
     * @Route({
     *     "fr" : "/article/nouveau",
     *     "en" : "/article/new",
     *     "es" : "/articulo/nuevo",
     * }, name="article_new", methods={"GET","POST"})
     * @param Request $request
     * @param Slugify $slugify
     * @return Response
     */
    public function new(Request $request, Slugify $slugify, \Swift_Mailer $mailer): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');


        if ($form->isSubmitted() && $form->isValid()) {
            $article->setSlug($slugify->generate($article->getTitle()));

            $author = $this->getUser();
            $article->setAuthor($author);

            $admin = $this->getUser();
            $article->setAuthor($admin);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();

            $mailTo = getenv('MAIL_TO');
            $mailFrom = getenv('MAIL_FROM');
            $message = (new \Swift_Message('Un nouvel article vient d\'être publié !'))
                ->setFrom($mailFrom)
                ->setTo($mailTo)
                ->setContentType('text/html')
                ->setBody($this->renderView('article/email/notification.html.twig', [
                    'article' => $article,
                ]));
            $mailer->send($message);

            $this->addFlash('success', 'Well Done ! That\'s a new article');

            return $this->redirectToRoute('article_index');
        }

        return $this->render('article/new.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="article_show", methods={"GET"})
     * @isGranted("ROLE_USER")
     */
    public function show(Article $article): Response
    {
        return $this->render('article/show.html.twig', [
            'article' => $article,
            'isFavorite' => $this->getUser()->isFavorite($article)
        ]);
    }

    /**
     * @Route("/{id}/edit", name="article_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Article $article
     * @param Slugify $slugify
     * @return Response
     */
    public function edit(Request $request, Article $article, Slugify $slugify): Response
    {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();

        if (!$this->isGranted('ROLE_ADMIN')) {
            if ($article->getAuthor()->getId() !== $user->getId()) {
                throw $this->createAccessDeniedException('Nope ! You can\'t ! Access only for Admin');
            }
        }

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setSlug($slugify->generate($article->getTitle()));
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Well Done ! You\'ve edited the article');

            return $this->redirectToRoute('article_index', [
                'id' => $article->getId(),
            ]);
        }

        return $this->render('article/edit.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="article_delete", methods={"DELETE"})
     * @IsGranted("ROLE_ADMIN", message="You can\'t ! Already said ! Only for Admin !")
     */
    public function delete(Request $request, Article $article): Response
    {
        if ($this->isCsrfTokenValid('delete' . $article->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($article);
            $entityManager->flush();

            $this->addFlash('danger', 'Well Done ! You\'ve deleted the article');
        }

        return $this->redirectToRoute('article_index');
    }

    /**
     * @Route("/{id}/favorite", name="article_favorite", methods={"GET","POST"})
     * @param Request $request
     * @param Article $article
     * @param ObjectManager $manager
     * @return Response
     */
    public function favorite(Request $request, Article $article, ObjectManager $manager): Response
    {
        if ($this->getUser()->getFavorite()->contains($article)) {
            $this->getUser()->removeFavorite($article);
        } else {
            $this->getUser()->addFavorite($article);
        }
        $manager->flush();
        return $this->json([
            'isFavorite' => $this->getUser()->isFavorite($article)
        ]);
    }
}
