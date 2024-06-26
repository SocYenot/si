<?php

namespace App\Controller;

use App\Form\Type\ChangeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ChangeController extends AbstractController
{
    /**
     * Constructor.
     */
    public function __construct(private readonly TranslatorInterface $translator)
    {
    }

    #[Route('/change', name: 'change')]
    public function change(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        $user = $this->getUser();

        if (!$user || !$user instanceof PasswordAuthenticatedUserInterface) {
            throw new AccessDeniedException($this->translator->trans('message.must_be_logged_in'));
        }

        $form = $this->createForm(ChangeType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $currentPassword = $form->get('currentPassword')->getData();
            if (!$passwordHasher->isPasswordValid($user, $currentPassword)) {
                $this->addFlash('error', $this->translator->trans('message.password_does_not_match'));
                return $this->redirectToRoute('change');
            }

            $newPassword = $form->get('newPassword')->getData();
            $confirmNewPassword = $form->get('confirmPassword')->getData();

            if ($newPassword !== $confirmNewPassword) {
                $this->addFlash('error', $this->translator->trans('message.confirm_password_does_not_match'));
                return $this->redirectToRoute('change');
            }

            $newPassword = $form->get('newPassword')->getData();
            $encodedPassword = $passwordHasher->hashPassword($user, $newPassword);
            $user->setPassword($encodedPassword);

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash(
                'success',
                $this->translator->trans('message.changed_successfully')
            );

            return $this->redirectToRoute('task_index');
        }

        return $this->render('change/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}