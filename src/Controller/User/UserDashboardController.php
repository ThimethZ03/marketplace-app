<?php

namespace App\Controller\User;

use App\Repository\AdvertRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class UserDashboardController extends AbstractController
{
    #[Route('/user/dashboard', name: 'app_user_dashboard')]
    public function index(AdvertRepository $advertRepository): Response
    {
        $user = $this->getUser();
        $userAdverts = $advertRepository->findByUser($user);

        return $this->render('user/dashboard.html.twig', [
            'user' => $user,
            'adverts' => $userAdverts,
            'advert_count' => count($userAdverts),
        ]);
    }
}
