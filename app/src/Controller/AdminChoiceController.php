<?php
/**
 * Admin Choice Controller.
 */

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AdminChoiceController.
 */
class AdminChoiceController extends AbstractController
{
    /**
     * Pick user from a list.
     *
     * @param EntityManagerInterface $entityManager Entity Manager
     *
     * @return Response HTTP response
     */
    #[\Symfony\Component\Routing\Attribute\Route('/admin/change', name: 'admin_choice')]
    public function userList(EntityManagerInterface $entityManager): Response
    {
        /** @var UserRepository $userRepository */
        $userRepository = $entityManager->getRepository(User::class);
        $users = $userRepository->findNonAdminUsers();

        return $this->render('change/choice.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * Change user.
     *
     * @param User $user User
     *
     * @return Response HTTP response
     */
    #[\Symfony\Component\Routing\Attribute\Route(path: '/admin/change/{id}', name: 'admin_change_user')]
    public function changeUser(User $user): Response
    {
        return $this->redirectToRoute('admin_change', ['id' => $user->getId()]);
    }
}
