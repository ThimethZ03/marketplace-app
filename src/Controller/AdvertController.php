<?php

namespace App\Controller;

use App\Entity\Advert;
use App\Repository\AdvertRepository;
use App\Repository\CategoryRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/adverts')]
class AdvertController extends AbstractController
{
    #[Route('/', name: 'app_advert_list')]
    public function list(
        Request $request,
        AdvertRepository $advertRepository,
        CategoryRepository $categoryRepository,
        PaginatorInterface $paginator
    ): Response {
        $search = $request->query->get('search');
        $categoryId = $request->query->get('category');

        $category = null;
        if ($categoryId) {
            $category = $categoryRepository->find($categoryId);
        }

        if ($search) {
            $queryBuilder = $advertRepository->searchAdverts($search, $category);
        } elseif ($category) {
            $queryBuilder = $advertRepository->findByCategory($category);
        } else {
            $queryBuilder = $advertRepository->findAllPublic();
        }

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            12
        );

        // important: use the method with counts
        $categories = $categoryRepository->findAllWithAdvertCount();

        return $this->render('advert/list.html.twig', [
            'pagination' => $pagination,
            'categories' => $categories,
            'selected_category' => $category,
        ]);
    }

    #[Route('/{id}', name: 'app_advert_show', requirements: ['id' => '\d+'])]
    public function show(Advert $advert, AdvertRepository $advertRepository): Response
    {
        $relatedAdverts = $advertRepository->findByCategory($advert->getCategory())
            ->setMaxResults(4)
            ->getQuery()
            ->getResult();

        // Remove current advert from related
        $relatedAdverts = array_filter($relatedAdverts, fn($a) => $a->getId() !== $advert->getId());

        return $this->render('advert/show.html.twig', [
            'advert' => $advert,
            'related_adverts' => array_slice($relatedAdverts, 0, 4),
        ]);
    }
}
