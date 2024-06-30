<?php
/**
 * Admin Choice Controller
 */
namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * class Admin Choice Controller
 */
class AdminChoiceController extends AbstractController
{
    /**
     * pick user from a list
     * @param EntityManagerInterface $entityManager
     *
     * @return Response
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
     * @param User $user
     *
     * @return Response
     */
    #[\Symfony\Component\Routing\Attribute\Route(path: '/admin/change/{id}', name: 'admin_change_user')]
    public function changeUser(User $user): Response
    {
        return $this->redirectToRoute('admin_change', ['id' => $user->getId()]);
    }
}
