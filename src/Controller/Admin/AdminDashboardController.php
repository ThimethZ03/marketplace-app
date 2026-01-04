<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Repository\AdvertRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminDashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_admin_dashboard')]
    public function index(
        AdvertRepository $advertRepository,
        UserRepository $userRepository
    ): Response {
        return $this->render('admin/dashboard.html.twig', [
            'total_adverts' => $advertRepository->countAdverts(),
            'total_users' => $userRepository->countUsers(),
            'total_managers' => $userRepository->countManagers(),
        ]);
    }

    #[Route('/users', name: 'app_admin_users')]
    public function manageUsers(
        UserRepository $userRepository,
        AdvertRepository $advertRepository,
        Request $request
    ): Response {
        $search = $request->query->get('search', '');
        $page = max(1, (int) $request->query->get('page', 1));
        $limit = 10;

        if ($search) {
            $users = $userRepository->searchUsers($search, $page, $limit);
            $totalUsers = $userRepository->countSearchResults($search);
        } else {
            $users = $userRepository->findBy([], ['createdAt' => 'DESC'], $limit, ($page - 1) * $limit);
            $totalUsers = $userRepository->countUsers();
        }

        $totalPages = ceil($totalUsers / $limit);

        return $this->render('admin/users.html.twig', [
            'users' => $users,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalUsers' => $totalUsers,
            'totalAds' => $advertRepository->countAdverts(),
            'totalManagers' => $userRepository->countManagers(),
            'search' => $search,
        ]);
    }

    #[Route('/moderators', name: 'app_admin_moderators')]
    public function moderatorActions(
        UserRepository $userRepository,
        AdvertRepository $advertRepository,
        Request $request
    ): Response {
        $search = $request->query->get('search', '');
        $page = max(1, (int) $request->query->get('page', 1));
        $limit = 10;

        $managers = $userRepository->findManagers($search, $page, $limit);
        $totalManagers = $userRepository->countManagers($search);
        $totalPages = ceil(max($totalManagers, 1) / $limit);

        return $this->render('admin/moderators.html.twig', [
            'managers' => $managers,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalUsers' => $userRepository->countUsers(),
            'totalAds' => $advertRepository->countAdverts(),
            'totalManagers' => $totalManagers,
            'search' => $search,
        ]);
    }

    #[Route('/user/{id}/delete', name: 'app_admin_user_delete', methods: ['POST'])]
    public function deleteUser(
        User $user,
        EntityManagerInterface $em,
        Request $request
    ): Response {
        // CSRF Protection
        $token = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('delete' . $user->getId(), $token)) {
            $this->addFlash('error', 'Invalid CSRF token.');
            return $this->redirectToRoute('app_admin_users');
        }

        // Prevent deleting yourself
        if ($user === $this->getUser()) {
            $this->addFlash('error', 'You cannot delete your own account.');
            return $this->redirectToRoute('app_admin_users');
        }

        $em->remove($user);
        $em->flush();

        $this->addFlash('success', 'User deleted successfully.');
        return $this->redirectToRoute('app_admin_users');
    }

    #[Route('/user/{id}/promote', name: 'app_admin_user_promote', methods: ['POST'])]
    public function promoteUser(
        User $user,
        EntityManagerInterface $em,
        Request $request
    ): Response {
        // CSRF Protection
        $token = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('promote' . $user->getId(), $token)) {
            $this->addFlash('error', 'Invalid CSRF token.');
            return $this->redirectToRoute('app_admin_moderators');
        }

        if (!in_array('ROLE_MANAGER', $user->getRoles())) {
            $roles = $user->getRoles();
            $roles[] = 'ROLE_MANAGER';
            $user->setRoles(array_unique($roles));
            $em->flush();
            $this->addFlash('success', $user->getFirstName() . ' promoted to Manager.');
        } else {
            $this->addFlash('info', 'User is already a Manager.');
        }

        return $this->redirectToRoute('app_admin_moderators');
    }

    #[Route('/user/{id}/demote', name: 'app_admin_user_demote', methods: ['POST'])]
    public function demoteUser(
        User $user,
        EntityManagerInterface $em,
        Request $request
    ): Response {
        // CSRF Protection
        $token = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('demote' . $user->getId(), $token)) {
            $this->addFlash('error', 'Invalid CSRF token.');
            return $this->redirectToRoute('app_admin_moderators');
        }

        $roles = array_filter($user->getRoles(), fn($role) => $role !== 'ROLE_MANAGER');
        $user->setRoles(array_values($roles));
        $em->flush();

        $this->addFlash('success', $user->getFirstName() . ' demoted from Manager.');
        return $this->redirectToRoute('app_admin_moderators');
    }
}
