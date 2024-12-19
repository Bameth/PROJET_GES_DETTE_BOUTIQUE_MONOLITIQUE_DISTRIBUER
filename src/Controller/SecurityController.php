<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SecurityController extends AbstractController
{
    #[Route('/security/login', name: 'app_security_login')]
    public function index(): Response
    {
        $htmlContent = file_get_contents('../view/security/login.html');
        return new Response($htmlContent);
    }
}
