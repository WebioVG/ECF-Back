<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use DateTime;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    private $manager;
    private $hasher;

    public function __construct(EntityManagerInterface $manager, UserPasswordHasherInterface $hasher)
    {
        $this->manager = $manager;
        $this->hasher = $hasher;
    }

    #[Route('/inscription', name: 'security_register')]
    public function register(Request $request): Response
    {
        $user = new User();
        $userForm = $this->createForm(UserType::class, $user);
        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {
            $user->setJoinedAt(DateTimeImmutable::createFromMutable(new DateTime()));
            $user->setPassword($this->hasher->hashPassword($user, $user->getPassword()));

            $this->manager->persist($user);
            $this->manager->flush();

            return $this->redirectToRoute('security_login');
        }

        return $this->render('security/inscription.html.twig', [
            'userForm' => $userForm->createView()
        ]);
    }

    #[Route('/connexion', name: 'security_login')]
    public function login(AuthenticationUtils $authenticationUtils)
    {
        $error = $authenticationUtils->getLastAuthenticationError();

        return $this->render('security/login.html.twig', [
            'error' => $error
        ]);
    }

    #[Route('/deconnexion', name: 'security_logout')]
    public function logout() {}
}
