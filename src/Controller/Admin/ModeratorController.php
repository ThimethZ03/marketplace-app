<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/moderators')]
#[IsGranted('ROLE_ADMIN')]
class ModeratorController extends AbstractController
{
    #[Route('/', name: 'app_admin_moderators')]
    public function index(
        Request $request,
        UserRepository $userRepository,
        PaginatorInterface $paginator
    ): Response {
        $search = $request->query->get('search');

        $queryBuilder = $userRepository->createQueryBuilder('u')
            ->where('u.roles LIKE :role')
            ->setParameter('role', '%ROLE_MANAGER%')
            ->orderBy('u.createdAt', 'DESC');

        if ($search) {
            $queryBuilder->andWhere('u.email LIKE :search OR u.firstName LIKE :search OR u.lastName LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('admin/moderator/actions.html.twig', [
            'pagination' => $pagination,
            'total_managers' => $userRepository->countManagers(),
        ]);
    }

    #[Route('/{id}/promote', name: 'app_admin_moderator_promote', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function promote(
        User $user,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid('promote'.$user->getId(), $request->request->get('_token'))) {
            $roles = $user->getRoles();
            if (!in_array('ROLE_MANAGER', $roles)) {
                $roles[] = 'ROLE_MANAGER';
                $user->setRoles($roles);
                $entityManager->flush();

                $this->addFlash('success', 'User promoted to Manager successfully!');
            } else {
                $this->addFlash('info', 'User is already a Manager!');
            }
        }

        return $this->redirectToRoute('app_admin_moderators');
    }

    #[Route('/{id}/demote', name: 'app_admin_moderator_demote', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function demote(
        User $user,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid('demote'.$user->getId(), $request->request->get('_token'))) {
            $roles = $user->getRoles();
            $roles = array_filter($roles, fn($role) => $role !== 'ROLE_MANAGER');
            $user->setRoles(array_values($roles));
            $entityManager->flush();

            $this->addFlash('success', 'Manager demoted to User successfully!');
        }

        return $this->redirectToRoute('app_admin_moderators');
    }
}
