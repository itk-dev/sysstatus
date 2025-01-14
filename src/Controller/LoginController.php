<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route(path: '/', name: 'app_login')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        // Get the login error if there is one.
        $error = $authenticationUtils->getLastAuthenticationError();

        // Last username entered by the user.
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login/index.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'admin_logout')]
    public function logout(): RedirectResponse
    {
        return $this->redirectToRoute('app_login');
    }
}
