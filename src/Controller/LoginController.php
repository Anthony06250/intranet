<?php

namespace App\Controller;

use App\Controller\Admin\DashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Translation\TranslatableMessage;

class LoginController extends AbstractController
{
    /**
     * @param AdminUrlGenerator $adminUrlGenerator
     */
    public function __construct(private readonly AdminUrlGenerator $adminUrlGenerator)
    {
    }

    /**
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    #[Route('/', name: 'app_login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED')) {
            return $this->redirect($this->adminUrlGenerator
                ->setController(DashboardController::class)
                ->setAction(Action::INDEX)
                ->generateUrl());
        }

        if ($error = $authenticationUtils->getLastAuthenticationError()) {
            $this->addFlash('error', new TranslatableMessage($error->getMessageKey(), [], 'security'));
        }

        return $this->render('bundles/EasyAdminBundle/page/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'translation_domain' => 'messages',
            'csrf_token_intention' => 'authenticate',
            'forgot_password_enabled' => true,
//            'forgot_password_path' => $this->generateUrl('...', ['...' => '...']),
            'remember_me_enabled' => true,
            'remember_me_checked' => true
        ]);
    }

    /**
     * @return void
     */
    #[Route('/logout', name: 'app_logout', methods: ['GET'])]
    public function logout(): void
    {
        // Nothing to do here
    }
}
