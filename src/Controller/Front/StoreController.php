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
use App\Repository\TagsRepository;
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
    public function index(ProductsRepository $productsRepository, Request $request, TagsRepository $tagsRepository): Response
    {


        $qb = $productsRepository->getAllProducts();
        $products = $this->paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            12
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
            'title' => 'SlimyMarket - search',
            'products' => $products
        ]);

    }

    #[Route('/category/{tag}', name: 'app_account_category', methods: ['GET'])]
    public function category(ProductsRepository $productsRepository,TagsRepository $tagsRepository, Request $request, string $tag): Response
    {
        $tagId = $tagsRepository->findOneBy(['name' => $tag]);
        $qb = $productsRepository->getAllProductsByTags($tagId->getId());

        $products = $this->paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            8
        );

        return $this->render('store/category.html.twig', [
            'title' => "SlimyMarket - ",
            'products' => $products
        ]);
    }
    #[Route('/delete/{id}', name: 'app_store_delete', methods: ['POST'])]
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

    #[Route('/{name}', name: 'app_store_details', methods: ['GET', 'POST'])]
    public function details(ProductsRepository $productsRepository, CommentsRepository $commentsRepository, UserRepository $userRepository, Products $products, string $name, Request $request): Response
    {
            //On prend le produit qui est détaillé.
            $product = $productsRepository->findOneBy(['name' => $name]);

            //On regarde les commentaires qui sont sur le produit demandé.
            $seeComments = $commentsRepository->getCommentsByProduct($product->getId());
            //On prend l'acheteur
            $users = $products->getBuyer();

            //On créer un nouveau commentaire
            $comments = new Comments();

            //Partie formulaire
            $form = $this->createForm(CommentType::class, $comments);
            $form->handleRequest($request);

            //Si le formulaire est envoyé et qu'il est valide on fait ...
        if ($form->isSubmitted() && $form->isValid()) {

            //On identifie la personne qui vient de faire un commentaire
            $userComment = $userRepository->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);

            //Ajout des informations par défaut du commentaire.
            $comments->setCreatedAt(new \DateTime());
            $comments->setProduct($product);
            $comments->setUser($userComment);

            //On sauvegarde le tout.
            $commentsRepository->save($comments, true);

            //Message succès comme quoi le commentaire a bien été envoyé.
            $this->addFlash('success', "Your comment is sent! An administrator must validate your comment!");

            //On retourne le tout.
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



}
