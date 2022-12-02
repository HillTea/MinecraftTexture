<?php

namespace App\Controller\Front;

use App\Repository\ProductsRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StoreController extends AbstractController
{


    public function __construct(private PaginatorInterface $paginator)
    {
    }

    #[Route('/store', name: 'app_store')]
    public function index(ProductsRepository $productsRepository, Request $request): Response
    {

        $qb = $productsRepository->getAllProducts();
        $products = $this->paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            16
        );


        return $this->render('store/index.html.twig', [
            'title' => 'Mc-Textures - Store',
            'products' => $products
        ]);
    }
}
