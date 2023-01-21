<?php

namespace App\Controller\Back;

use App\Repository\ProductsRepository;
use App\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{


    public function __construct( private PaginatorInterface $paginator)
    {
    }

    #[Route('/admin', name: 'app_admin')]
    public function index(ProductsRepository $productsRepository): Response
    {

        $products = $productsRepository->getAllProducts();

        return $this->render('admin/index.html.twig', [
            'title' => 'Admin - SlimyMarket',
            'products' => $products
        ]);
    }

    #[Route('/admin/products', name: 'app_admin_products')]
    public function products(ProductsRepository $productsRepository, Request $request): Response
    {

        $qb = $productsRepository->getAllProducts();

        $products = $this->paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            8
        );

        return $this->render('admin/produits.html.twig', [
            'title' => 'Admin - SlimyMarket',
            'products' => $products
        ]);
    }


    #[Route('/admin/users', name: 'app_admin_users')]
    public function users(UserRepository $usersRepository, Request $request): Response
    {

        $qb = $usersRepository->findAll();

        $users = $this->paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            8
        );

        return $this->render('admin/users.html.twig', [
            'title' => 'Admin - SlimyMarket',
            'users' => $users
        ]);
    }
}
