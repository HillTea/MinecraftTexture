<?php

namespace App\Controller\Front;

use App\Repository\ProductsRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{


    #[Route('/', name: 'app_index')]
    public function index(): Response
    {



        return $this->render('index/index.html.twig', [
            'title' => 'Mc-Textures'
        ]);
    }
}
