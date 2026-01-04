<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Repository\AdvertRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class UserManagementController extends AbstractController
{
    #[Route('/users', name: 'app_admin_users')]
    public function manageUsers(
        Request $request,
        UserRepository $userRepository,
        AdvertRepository $advertRepository,
        PaginatorInterface $paginator
    ): Response {
        $search = $request->query->get('search', '');

        $queryBuilder = $userRepository->createQueryBuilder('u')
            ->orderBy('u.createdAt', 'DESC');

        if ($search) {
            $queryBuilder->where('u.email LIKE :search OR u.firstName LIKE :search OR u.lastName LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('admin/user/manage.html.twig', [
            'users' => $pagination,
            'currentPage' => $pagination->getCurrentPageNumber(),
            'totalPages' => ceil($pagination->getTotalItemCount() / 10),
            'totalUsers' => $userRepository->countUsers(),
            'totalAds' => $advertRepository->countAdverts(),
            'totalManagers' => $userRepository->countManagers(),
            'search' => $search,
        ]);
    }

    #[Route('/moderators', name: 'app_admin_moderators')]
    public function moderatorActions(
        Request $request,
        UserRepository $userRepository,
        AdvertRepository $advertRepository,
        PaginatorInterface $paginator
    ): Response {
        $search = $request->query->get('search', '');

        // CHANGED: Remove the role filter to show ALL users
        $queryBuilder = $userRepository->createQueryBuilder('u')
            ->orderBy('u.createdAt', 'DESC');

        if ($search) {
            $queryBuilder->where('u.email LIKE :search OR u.firstName LIKE :search OR u.lastName LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('admin/moderator/actions.html.twig', [
            'managers' => $pagination,
            'currentPage' => $pagination->getCurrentPageNumber(),
            'totalPages' => ceil($pagination->getTotalItemCount() / 10),
            'totalUsers' => $userRepository->countUsers(),
            'totalAds' => $advertRepository->countAdverts(),
            'totalManagers' => $userRepository->countManagers(),
            'search' => $search,
        ]);
    }

    #[Route('/user/{id}/delete', name: 'app_admin_user_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function deleteUser(
        User $user,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            if ($user === $this->getUser()) {
                $this->addFlash('error', 'You cannot delete your own account!');
                return $this->redirectToRoute('app_admin_users');
            }

            $entityManager->remove($user);
            $entityManager->flush();

            $this->addFlash('success', 'User deleted successfully!');
        }

        return $this->redirectToRoute('app_admin_users');
    }

    #[Route('/user/{id}/promote', name: 'app_admin_user_promote', methods: ['POST'])]
    public function promoteUser(
        User $user,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        if (!$this->isCsrfTokenValid('promote' . $user->getId(), $request->request->get('_token'))) {
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
        Request $request,
        EntityManagerInterface $em
    ): Response {
        if (!$this->isCsrfTokenValid('demote' . $user->getId(), $request->request->get('_token'))) {
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
