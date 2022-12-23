<?php

namespace App\Controller\Front;

use App\Entity\Comments;
use App\Entity\Products;
use App\Entity\User;
use App\Form\CommentType;
use App\Form\Products1Type;
use App\Form\RessourceType;
use App\Repository\CommentsRepository;
use App\Repository\ProductsRepository;
use App\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/store')]
class StoreController extends AbstractController
{

    public function __construct(private PaginatorInterface $paginator)
    {
    }


    #[Route('/', name: 'app_store', methods: ['GET'])]
    public function index(ProductsRepository $productsRepository, Request $request): Response
    {

        $qb = $productsRepository->getAllProducts();
        $products = $this->paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            16
        );

        return $this->render('store/index.html.twig', [
            'title' => "Mc-textures - Products",
            'products' => $products,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_store_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Products $product, ProductsRepository $productsRepository): Response
    {
        $form = $this->createForm(RessourceType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productsRepository->save($product, true);

            return $this->redirectToRoute('app_store', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('store/edit.html.twig', [
            'product' => $product,
            'id' => $product->getId(),
            'form' => $form,
        ]);
    }

    #[Route('/search', name: 'app_store_search', methods: ['GET'])]
    public function search(Request $request, ProductsRepository $productsRepository): Response
    {

//        $request->query->get("search")
        $qb = $productsRepository->getSearchedProduct($request->query->get("search"));
        $products = $this->paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            8
        );
//        $product = $productsRepository->findOneBy(['name' => $name]);

        return $this->render('store/search.html.twig', [
            'title' => 'Search',
            'products' => $products
        ]);

    }

    #[Route('/{name}', name: 'app_store_details', methods: ['GET', 'POST'])]
    public function details(ProductsRepository $productsRepository, CommentsRepository $commentsRepository,UserRepository $userRepository, Products $products, string $name, Request $request): Response
    {

            $product = $productsRepository->findOneBy(['name' => $name]);
            $seeComments = $commentsRepository->getCommentsByProduct($product->getId());
            $users = $products->getBuyer();
            $userComment = $userRepository->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);

            $comments = new Comments();

            $form = $this->createForm(CommentType::class, $comments);
            $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $comments->setCreatedAt(new \DateTime());
            $comments->setProduct($product);
            $comments->setUser($userComment);

            $commentsRepository->save($comments, true);

            $this->addFlash('success', "Your comment is sent! An administrator must validate your comment!");

            return $this->redirectToRoute('app_store_details', ['name' => $products->getName()], Response::HTTP_SEE_OTHER);

        }

        return $this->render('store/details.html.twig', [
            'title' => 'Mc-Textures - ' . $product->getName(),
            'product' => $product,
            'users' => $users,
            'comment' => $form->createView(),
            'comments' => $seeComments

        ]);
    }

    #[Route('/{id}', name: 'app_store_delete', methods: ['POST'])]
    public function delete(Request $request, Products $product, ProductsRepository $productsRepository): Response
    {

        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $this->addFlash("success", "Your product has been deleted.");
            $productsRepository->remove($product, true);

            return $this->redirectToRoute('app_account', ['pseudo' => $product->getSeller()->getPseudo()], Response::HTTP_SEE_OTHER);
        }
        $this->addFlash("error", "An error has occurred! Please contact an admin.");
        return $this->redirectToRoute('app_account', ['pseudo' => $product->getSeller()->getPseudo()], Response::HTTP_SEE_OTHER);
    }

}
