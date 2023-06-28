<?php
/**
 * Hello controller.
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class HelloController.
 */
#[Route('/')]
class IndexController extends AbstractController
{
    /**
     * Index action.
     *
     * @return Response HTTP response
     */
    #[Route(
        '/',
        name: 'index',
        methods: 'GET'
    )]


    public function index(): Response
    {
        return $this->render(
            'index/index.html.twig'
        );
    }
}
