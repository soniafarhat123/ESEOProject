<?php

namespace App\Controller;


use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/user', name: 'user.profil', methods: ['GET'])]
    public function profil(): Response
    {
        $user = $this->getUser();
            return $this->render('pages/user/profil.html.twig',
                [
                    'user' => $user
                ]);
    }
}
