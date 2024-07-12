<?php
/**
 * Change controller.
 */

namespace App\Controller;

use App\Form\Type\PasswordChangeType;
use App\Form\Type\EmailChangeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class Change Controller.
 */
class ChangeController extends AbstractController
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
     * change account data.
     *
     * @param Request                     $request        The current HTTP request
     * @param UserPasswordHasherInterface $passwordHasher The password hasher service
     * @param EntityManagerInterface      $entityManager  The entity manager for persisting changes
     *
     * @return Response The HTTP response
     */
    #[\Symfony\Component\Routing\Attribute\Route('/change', name: 'change')]
    public function change(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$user || !$user instanceof PasswordAuthenticatedUserInterface) {
            throw new AccessDeniedException($this->translator->trans('message.must_be_logged_in'));
        }

        $passwordForm = $this->createForm(PasswordChangeType::class);
        $emailForm = $this->createForm(EmailChangeType::class);

        $passwordForm->handleRequest($request);
        $emailForm->handleRequest($request);

        if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
            $currentPassword = $passwordForm->get('currentPassword')->getData();
            if (!$passwordHasher->isPasswordValid($user, $currentPassword)) {
                $this->addFlash('error', $this->translator->trans('message.password_does_not_match'));

                return $this->redirectToRoute('change');
            }

            $newPassword = $passwordForm->get('newPassword')->getData();
            $confirmNewPassword = $passwordForm->get('confirmPassword')->getData();

            if ($newPassword !== $confirmNewPassword) {
                $this->addFlash('error', $this->translator->trans('message.confirm_password_does_not_match'));

                return $this->redirectToRoute('change');
            }

            $encodedPassword = $passwordHasher->hashPassword($user, $newPassword);
            $user->setPassword($encodedPassword);

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', $this->translator->trans('message.changed_successfully'));

            return $this->redirectToRoute('task_index');
        }

        if ($emailForm->isSubmitted() && $emailForm->isValid()) {
            $newEmail = $emailForm->get('newEmail')->getData();
            $confirmNewEmail = $emailForm->get('confirmEmail')->getData();

            if ($newEmail !== $confirmNewEmail) {
                $this->addFlash('error', $this->translator->trans('message.confirm_email_does_not_match'));

                return $this->redirectToRoute('change');
            }

            if ($user->getEmail() !== $newEmail) {
                $user->setEmail($newEmail);

                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', $this->translator->trans('message.changed_successfully'));

                return $this->redirectToRoute('task_index');
            }
        }

        return $this->render('change/index.html.twig', [
            'passwordForm' => $passwordForm->createView(),
            'emailForm' => $emailForm->createView(),
        ]);
    }
}
