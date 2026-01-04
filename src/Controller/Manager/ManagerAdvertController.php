<?php

namespace App\Controller\Manager;

use App\Entity\Advert;
use App\Form\AdvertFormType;
use App\Repository\AdvertRepository;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/manager')]
#[IsGranted('ROLE_MANAGER')]
class ManagerAdvertController extends AbstractController
{
    #[Route('/dashboard', name: 'app_manager_dashboard')]
    public function dashboard(
        Request $request,
        AdvertRepository $advertRepository,
        UserRepository $userRepository,
        CategoryRepository $categoryRepository,
        PaginatorInterface $paginator
    ): Response {
        $search = $request->query->get('search', '');
        $category = $request->query->get('category', '');

        $queryBuilder = $advertRepository->createQueryBuilder('a')
            ->leftJoin('a.user', 'u')
            ->leftJoin('a.category', 'c')
            ->addSelect('u', 'c')
            ->orderBy('a.createdAt', 'DESC');

        if ($search) {
            $queryBuilder->where('a.title LIKE :search OR u.firstName LIKE :search OR u.lastName LIKE :search OR a.id LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        if ($category && $category !== 'all') {
            $queryBuilder->andWhere('c.name = :category')
                ->setParameter('category', $category);
        }

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('manager/advert/manage.html.twig', [
            'adverts' => $pagination,
            'currentPage' => $pagination->getCurrentPageNumber(),
            'totalPages' => ceil($pagination->getTotalItemCount() / 10),
            'totalAds' => $advertRepository->countAdverts(),
            'totalUsers' => $userRepository->countUsers(),
            'categories' => $categoryRepository->findAll(),
            'search' => $search,
            'selectedCategory' => $category,
        ]);
    }

    #[Route('/advert/{id}/edit', name: 'app_manager_advert_edit')]
    public function editAdvert(
        Advert $advert,
        Request $request,
        EntityManagerInterface $em,
        CategoryRepository $categoryRepository,
        FileUploader $fileUploader = null
    ): Response {
        $form = $this->createForm(AdvertFormType::class, $advert);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle image upload - check if field exists first
            if ($form->has('imagePath')) {
                /** @var UploadedFile $imageFile */
                $imageFile = $form->get('imagePath')->getData();
                
                if ($imageFile && $fileUploader) {
                    $newFilename = $fileUploader->upload($imageFile);
                    $advert->setImage($newFilename);
                }
            }

            $em->flush();

            $this->addFlash('success', 'Advertisement updated successfully!');
            return $this->redirectToRoute('app_manager_dashboard');
        }

        return $this->render('manager/advert/edit.html.twig', [
            'form' => $form->createView(),
            'advert' => $advert,
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    #[Route('/advert/{id}/delete', name: 'app_manager_advert_delete', methods: ['POST'])]
    public function deleteAdvert(
        Advert $advert,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        if ($this->isCsrfTokenValid('delete' . $advert->getId(), $request->request->get('_token'))) {
            $em->remove($advert);
            $em->flush();

            $this->addFlash('success', 'Advertisement deleted successfully!');
        }

        return $this->redirectToRoute('app_manager_dashboard');
    }
}
