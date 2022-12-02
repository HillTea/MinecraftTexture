<?php

namespace App\Controller\Front;

use App\Repository\ProductsRepository;
use App\Repository\UserRepository;
use Faker\Core\Uuid;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{


    public function __construct(
        private PaginatorInterface $paginator
    )
    {
    }

    #[Route('/account/{uuid}', name: 'app_account')]
    public function index(UserRepository $repository, string $pseudo, ProductsRepository $productsRepository, Request $request): Response
    {

        $user = $repository->findOneBy(['pseudo' => $pseudo]);
        $qb = $productsRepository->getProductsByUser($user);
        $pagination = $this->paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            5
        );


        if(!$user){
            throw $this->createNotFoundException(
                'No user got the name: ' . $pseudo . ' Please refer an newer name!'
            );
        }


        $product = $productsRepository->findBy(['seller' => $user->getId()]);

        return $this->render('account/index.html.twig', [
            'title' => 'Mc-Textures - Account',
            'users' => $user,
            'products' => $pagination
        ]);
    }
}
