<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{

    /**
     * @return Response
     * @Route("/blog", name="blog_index")
     */
    public function index()
    {
        return new Response(
            '<html><body>Blog Index</body></html>'
        );
    }

    /**
     * @return Response
     * @Route("/blog", name="blog_index")
     */
    public function owner()
    {
        return $this->render('blog/index.html.twig', [
            'owner' => 'Sylvain',
        ]);
    }
}
