<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Form\UserEditType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    private UserPasswordHasherInterface $hasher;
    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    /**
     * This function allow us to sign in
     *
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */

    #[Route('/connexion', name: 'security.login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if($this->getUser()){
            return $this->redirectToRoute('home.index');
        }
        else {
            $lastUsername = $authenticationUtils->getLastUsername();
            $error = $authenticationUtils->getLastAuthenticationError();

            return $this->render('pages/security/login.html.twig', [
                'last_username' => $lastUsername,
                'error' => $error
            ]);
        }
    }

    /**
     * This function allow us to logour
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    #[Route('/deconnexion', name: 'security.logout')]
    public function logout(){

        return $this->redirectToRoute('security.login');
    }

    /**
     * This function allow us to register
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     * @throws \Exception
     */

    #[Route('/inscription', 'security.registration', methods: ['GET', 'POST'])]
    public function registration(Request $request,EntityManagerInterface $manager): Response
    {
        $user = new User();
        $user->setRoles(['ROLE_USER']);

        $form = $this->createForm(RegistrationType::class, $user);


        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $user->setPassword($this->hasher->hashPassword(
                $user,
                $user->getPassword()
            ));
            if($user->getImage() != null) {
                // On récupère les images transmises
                $file = $form->get('image')->getData();
                // On génère un nouveau nom de fichier
                $fileName = bin2hex(random_bytes(6)) . '.' . $file->guessExtension();
                // On copie le fichier dans le dossier uploads
                $file->move($this->getParameter('user_directory'), $fileName);
                // On crée l'image dans la base de données
                $user->setImage($fileName);
            }

            $this->addFlash(
                'success',
                'Votre compte a été créé avec succés.'
            );
            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('security.login');
        }

        return $this->render('pages/security/registration.html.twig',[
            'form' => $form->createView()
        ]);
    }

    #[Route('/supprimerCompte/{id}',name: "security.delete", methods: ['GET','POST'])]
    public function deleteAccount(EntityManagerInterface $manager) : Response
    {
        $user = $this->getUser();
        $this->container->get('security.token_storage')->setToken(null);
        $manager->remove($user);
        $manager->flush();
        $this->addFlash('success',
            'Votre compte utilisateur a bien été supprimé !');
        return $this->redirectToRoute('home.index');

    }

}
