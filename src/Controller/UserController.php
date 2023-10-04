<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Form\UserDataType;
use Doctrine\ORM\EntityManagerInterface;
use ErrorException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\VarDumper\VarDumper;

class UserController extends AbstractController
{
    public function login(Request $request): Response
    {
        $this->addFlash('success', 'Success authorization!');

        return $this->render('user/login.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    public function registration(
        UserPasswordHasherInterface $passwordHasher,
        Request $request,
        EntityManagerInterface $em
    ): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setSalt(
                base_convert(sha1(uniqid(mt_rand(), true)), 16, 36)
            );
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $user->getPassword()
            );
            $user->setPassword($hashedPassword);
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'Success registration!');
            return $this->redirectToRoute('index');
        }

        return $this->render('user/registration.html.twig', [
            'form' => $form
        ]);
    }

    public function account(
        UserInterface $user
    ): Response
    {
        $form = $this->createForm(UserDataType::class, $user);

        return $this->render('user/account.html.twig', [
            'form' => $form
        ]);
    }

    public function change(
        Request $request,
        UserInterface $user,
        SluggerInterface $slugger,
        EntityManagerInterface $em
    ): Response
    {
        $form = $this->createForm(UserDataType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $avatar = $form->get('avatar')->getData();
            if ((!$avatar && !$user->getAvatar()) || ($user->getAvatar() && $avatar)) {
                $this->addFlash('danger', 'Something went wrong!');
                $this->redirectToRoute('index');
            }
            if ($avatar) {
                $originalFilename = pathinfo($avatar->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $path = sprintf('/img/avatars/%s/', $user->getId());
                $newFilename = sprintf('%s-%s.%s', substr($safeFilename, 0, 150), uniqid(), $avatar->guessExtension());
                $filePath = sprintf('%s/public%s', $this->getParameter('kernel.project_dir'), $path);
                $avatar->move(
                    $filePath,
                    $newFilename
                );
                $user->setAvatar(sprintf('%s%s', $path, $newFilename));
            }
            try {
                $em->persist($user);
                $em->flush();
                $this->addFlash('success', 'Room was successful added!');
            } catch (ErrorException) {
                $this->addFlash('danger', 'Something went wrong!');
            }
        }

        return $this->redirectToRoute('user_account');
    }
}
