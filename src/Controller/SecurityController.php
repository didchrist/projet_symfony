<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityController extends AbstractController
{
    #[Route("/inscription", name: "security_registration")]
    public function registration(Request $request, UserRepository $repo, UserPasswordHasherInterface $encoder)
    {
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            $hash = $encoder->hashPassword($user, $user->getPassword());

            $user->setPassword($hash);
            $repo->save($user, true);
            return $this->redirectToRoute('security_login');
        }

        return $this->render('security/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }
    #[Route('/login', name: "security_login")]
    public function login()
    {
        return $this->render('security/login.html.twig');
    }
    #[Route('/deconnexion', name: "security_logout")]
    public function logout()
    {
    }
}
