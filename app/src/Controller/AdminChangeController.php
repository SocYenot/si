<?php
/**
 * Admin Change Controller.
 */

namespace App\Controller;

use App\Form\Type\PasswordChangeType;
use App\Form\Type\AdminEmailChangeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Entity\User;

/**
 * Class Admin Change Controller.
 */
class AdminChangeController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param TranslatorInterface $translator Translator
     */
    public function __construct(private readonly TranslatorInterface $translator)
    {
    }

    /**
     * Change other user's account data.
     *
     * @param Request                     $request        Request
     * @param UserPasswordHasherInterface $passwordHasher Password Hasher
     * @param EntityManagerInterface      $entityManager  Entity Manager
     * @param User                        $user           User
     *
     * @return Response HTTP response
     */
    #[\Symfony\Component\Routing\Attribute\Route('/admin/change/{id}', name: 'admin_change')]
    public function change(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager, User $user): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $passwordForm = $this->createForm(PasswordChangeType::class);
        $emailForm = $this->createForm(AdminEmailChangeType::class);

        $passwordForm->handleRequest($request);
        $emailForm->handleRequest($request);

        if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
            $currentPassword = $passwordForm->get('currentPassword')->getData();
            $newPassword = $passwordForm->get('newPassword')->getData();
            $confirmNewPassword = $passwordForm->get('confirmPassword')->getData();

            if (!$passwordHasher->isPasswordValid($user, $currentPassword)) {
                $this->addFlash('error', $this->translator->trans('message.invalid_current_password'));

                return $this->redirectToRoute('admin_change', ['id' => $user->getId()]);
            }

            if ($newPassword !== $confirmNewPassword) {
                $this->addFlash('error', $this->translator->trans('message.confirm_password_does_not_match'));

                return $this->redirectToRoute('admin_change', ['id' => $user->getId()]);
            }

            $encodedPassword = $passwordHasher->hashPassword($user, $newPassword);
            $user->setPassword($encodedPassword);

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', $this->translator->trans('message.changed_successfully'));

            return $this->redirectToRoute('admin_change', ['id' => $user->getId()]);
        }

        if ($emailForm->isSubmitted() && $emailForm->isValid()) {
            $currentEmail = $emailForm->get('currentEmail')->getData();
            $newEmail = $emailForm->get('newEmail')->getData();
            $confirmNewEmail = $emailForm->get('confirmEmail')->getData();

            if ($currentEmail !== $user->getEmail()) {
                $this->addFlash('error', $this->translator->trans('message.invalid_current_email'));

                return $this->redirectToRoute('admin_change', ['id' => $user->getId()]);
            }

            if ($newEmail !== $confirmNewEmail) {
                $this->addFlash('error', $this->translator->trans('message.confirm_email_does_not_match'));

                return $this->redirectToRoute('admin_change', ['id' => $user->getId()]);
            }

            if ($user->getEmail() !== $newEmail) {
                $user->setEmail($newEmail);

                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', $this->translator->trans('message.changed_successfully'));

                return $this->redirectToRoute('admin_change', ['id' => $user->getId()]);
            }
        }

        return $this->render('change/admin.html.twig', [
            'passwordForm' => $passwordForm->createView(),
            'emailForm' => $emailForm->createView(),
            'user' => $user,
        ]);
    }
}
