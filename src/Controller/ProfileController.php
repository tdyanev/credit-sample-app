<?php

namespace App\Controller;

use App\Repository\CreditRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/profile')]
class ProfileController extends AbstractController
{
    #[Route('/', name: 'app_profile')]
    public function index(): Response
    {
        return $this->render('profile/index.html.twig', [
            'controller_name' => $this->getUser()->getEntireCreditAmount(),
        ]);
    }

    #[Route('/my_credits', name: 'app_my_credits')]
    public function my_credits(CreditRepository $cr): Response {
        return $this->render('credit/index.html.twig', [
            'credits' => $this->getUser()->getCredits(),
            'controller_name' => 'CREDITS!!',
        ]);
    }
}
