<?php

namespace App\Controller;

use App\Repository\AdvertRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        CategoryRepository $categoryRepository,
        AdvertRepository $advertRepository
    ): Response {
        // Load all categories; advert counts are handled by Category methods
        $categories = $categoryRepository->findAllWithAdvertCount();

        // Get featured adverts (latest 8)
        $featuredAdverts = $advertRepository->findBy(
            [],
            ['createdAt' => 'DESC'],
            8
        );

        return $this->render('home/index.html.twig', [
            'categories' => $categories,
            'featured_adverts' => $featuredAdverts,
        ]);
    }
}
