<?php

namespace App\Controller\Front;

use App\Entity\Products;
use App\Entity\Tags;
use App\Entity\User;
use App\Form\RessourceType;
use App\Form\UserType;
use App\Repository\ProductsRepository;
use App\Repository\TagsRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/account')]
class AccountController extends AbstractController
{
    public function __construct(
        private PaginatorInterface $paginator
    )
    {
    }

    #[Route('/{pseudo}', name: 'app_account', methods: ['GET', 'POST'])]
    public function show(User $user, ProductsRepository $productsRepository, Request $request, UserRepository $userRepository): Response
    {

        $qb = $productsRepository->getProductsByUser($user);
        $pagination = $this->paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            5
        );

        $qc = $user->getBuyer();
        $secondPagination = $this->paginator->paginate(
            $qc,
            $request->query->getInt('page', 1),
            5
        );

//        Ceci représente le formulaire d'information
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //Mise en place de l'avatar
            $uploadedFile = $form['pathImage']->getData();

            if($uploadedFile){
                $destination = $this->getParameter('kernel.project_dir'). '/public/images/users/' . $user->getId() . "/avatar/";
                $filesytem = new Filesystem();
                $filesytem->remove($destination);
                $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename.'-'.uniqid().'.'.$uploadedFile->guessExtension();
                $uploadedFile->move(
                    $destination,
                    $newFilename
                );
                $user->setPathImage("/images/users/" . $user->getId() . "/avatar/" . $newFilename);
            }
            //Fin de la mise en place de l'avatar

            //Mise en place de Banner
            $uploadedBannerFile = $form['path_banner']->getData();
            if($uploadedBannerFile){
                $destinationBannerFile = $this->getParameter('kernel.project_dir'). '/public/images/users/' . $user->getId() . "/banner/";
                $filesytem = new Filesystem();
                $filesytem->remove($destinationBannerFile);
                $originalBannerFilename = pathinfo($uploadedBannerFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newBannerFilename = $originalBannerFilename.'-'.uniqid().'.'.$uploadedBannerFile->guessExtension();
                $uploadedBannerFile->move(
                    $destinationBannerFile,
                    $newBannerFilename
                );
                $user->setPathBanner("/images/users/" . $user->getId() . "/banner/" . $newBannerFilename);
            }

            $userRepository->save($user, true);

            return $this->redirectToRoute('app_account', ['pseudo' => $user->getPseudo()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('account/index.html.twig', [
            'user' => $user,
            'title' => "Mc-textures - " . $user->getPseudo(),
            'products' => $pagination,
            'product' => $secondPagination,
            'form' => $form->createView(),
        ]);
    }
    #[Route('/{pseudo}/delete', name: 'app_account_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {

        dd($request);
        if ($this->isCsrfTokenValid('delete'.$user->getPseudo(), $request->request->get('_token'))) {
            $userRepository->remove($user, true);
            return $this->redirectToRoute('app_admin_users', [], Response::HTTP_SEE_OTHER);
        }

        return $this->redirectToRoute('app_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{pseudo}/add/ressource', name: 'app_account_ressources', methods: ['GET', 'POST'])]
    public function addRessources(string $pseudo, Request $request,TagsRepository $tagsRepository, UserRepository $repository, EntityManagerInterface $entityManager): Response
    {

        $user = $repository->findOneBy(['pseudo' => $pseudo]);
        $products = new Products();

        //Catégorie des ressources ajoutés par l'utilisateur sur son compte.

        $ressources = $this->createForm(RessourceType::class, $products);
        $ressources->handleRequest($request);

        if ($ressources->isSubmitted() && $ressources->isValid()) {

            $tagId = $tagsRepository->findOneBy(['name' => $request->request->get('tags')]);
            $tag = $tagsRepository->find($tagId);
            $this->addFlash('success', "Successfully added!");
            $products->setSeller($user);
            $products->setReview((float)null);
            $products->setCreatedAt(new \DateTime());
            $products->setLastUpdate(new \DateTime());



            if($ressources['pathImage']->getData() != null){

                //Image en avant du site.
                $uploadedProductFile = $ressources['pathImage']->getData();
                if($uploadedProductFile){
                    $destinationProductFile = $this->getParameter('kernel.project_dir'). '/public/images/users/' . $user->getId() . "/products/";
                    $filesytem = new Filesystem();
                    $filesytem->remove($destinationProductFile);
                    $originalProductFilename = pathinfo($uploadedProductFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $newProductFilename = $originalProductFilename.'-'.uniqid().'.'.$uploadedProductFile->guessExtension();
                    $uploadedProductFile->move(
                        $destinationProductFile,
                        $newProductFilename
                    );
                    $products->setPathImage("/images/users/" . $user->getId() . "/products/" . $newProductFilename);
                }

            } else {
                $products->setPathImage("/images/placeholders/278x540.png");
            }

            //Ressource du produit.
            $uploadedRessourceFile = $ressources['ressource']->getData();
            if($uploadedRessourceFile){
                $destinationRessourceFile = $this->getParameter('kernel.project_dir'). '/public/ressources/users/' . $user->getId() . "/products/" . $ressources['name']->getData();
                $filesytem = new Filesystem();
                $filesytem->remove($destinationRessourceFile);
                $originalRessourceFilename = pathinfo($uploadedRessourceFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newRessourceFilename = $originalRessourceFilename.'-'.uniqid().'.'.$uploadedRessourceFile->guessExtension();
                $uploadedRessourceFile->move(
                    $destinationRessourceFile,
                    $newRessourceFilename
                );
                $products->setRessource("/ressources/users/" . $user->getId() . "/products/" . $ressources['name']->getData() . "/" . $newRessourceFilename);
            }


            //Image concernant le détails du produit.
            $uploadedProductImageDetails = $ressources['pathLittleImage']->getData();
            if($uploadedProductImageDetails){
                $destinationProductImageDetails = $this->getParameter('kernel.project_dir'). '/public/images/users/' . $user->getId() . "/products/". $ressources['name']->getData() ."/details/images/";
                $filesytem = new Filesystem();
                $filesytem->remove($destinationProductImageDetails);
                $originalProductImageDetails = pathinfo($uploadedProductImageDetails->getClientOriginalName(), PATHINFO_FILENAME);
                $newProductImageDetails = $originalProductImageDetails.'-'.uniqid().'.'.$uploadedProductImageDetails->guessExtension();
                $uploadedProductImageDetails->move(
                    $destinationProductImageDetails,
                    $newProductImageDetails
                );
                $products->setPathLittleImage("/images/users/" . $user->getId() . "/products/". $ressources['name']->getData() ."/details/images/" . $newProductImageDetails);
            }

            $products->addTag($tag);

            $entityManager->persist($products);
            $entityManager->flush();


            return $this->redirectToRoute('app_account', ['pseudo'=> $user->getPseudo()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('account/ressources.html.twig', [
            'title' => 'Mc-Textures - Add Ressources',
            'ressourcesForm' => $ressources->createView(),
            'user' => $user
        ]);
    }
}
