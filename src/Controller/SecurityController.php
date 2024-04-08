<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Entity\Demandeformateur;
use App\Repository\UserRepository;
use App\Repository\DemandeformateurRepository;
use Symfony\Component\Security\Core\Security;

class SecurityController extends AbstractController
{
    private $passwordEncoder;
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }


    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }


    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $passwordhashed = $this->passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($passwordhashed);
            //image upload
            // $photo=$request->files->get("user_photo");
            $photo = $form['photo']->getData();
            if ($photo) {
                $photo_name = $photo->getClientOriginalName();
                $photo->move($this->getParameter("photo_directory"), $photo_name);
                $user->setPhoto($photo_name);
            }
            //------
            $entityManager = $this->getDoctrine()->getManager();
            $inputValueRole = $user->getRole();
            if ($inputValueRole == true) {
                $user->setRole("userformateur");
            } else {
                $user->setRole("user");
            }
            $entityManager->persist($user);
            $entityManager->flush();

            // You can add a flash message here to notify the user about successful registration.
            $this->addFlash('success', 'Registration successful!');

            return $this->redirectToRoute('app_login'); // Redirect to the login page after successful registration.
        }

        return $this->render('Security/register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/GestionUser",name="GestionUser")
     */
    public function GestionUser(UserRepository $userrepository, DemandeformateurRepository $demandeformateurrepository, Security $security)
    {
        $demande = $userrepository->findBy(['role' => 'userformateur']);
        $entityManager = $this->getDoctrine()->getManager();
        $demandeformateur = new Demandeformateur();
        foreach ($demande as $user) {
            $existingDemandeformateur = $demandeformateurrepository->findBy(['user' => $user]);
            if (!$existingDemandeformateur) {
                $demandeformateur->setUser($user);
                $demandeformateur->setEtat(0);
                $entityManager->persist($demandeformateur);
            }
        }

        $entityManager->flush();
        $demandesformateur = $demandeformateurrepository->findAll();
        if ($security->getUser()->getRole() == 'admin') {
            return $this->render('GestionUser/demande.html.twig', [
                'demandeformateur' => $demandesformateur
            ]);
        }
        return $this->redirectToRoute('home');
    }
    /**
     * @Route("/GestionUser/true/{iduser}",name="GestionUser_true")
     */
    public function GestionUsertrue(UserRepository $userrepository, DemandeformateurRepository $demandeformateurrepository, $iduser)
    {
        $user = $userrepository->findOneBy(['id' => $iduser]);
        $demandesformateur = $demandeformateurrepository->findOneBy(['user' => $user]);
        $entityManager = $this->getDoctrine()->getManager();
        $user->setRole('formateur');
        $entityManager->remove($demandesformateur);
        $entityManager->flush();
        return $this->redirectToRoute('showformation');
    }
    /**
     * @Route("/GestionUser/false/{iduser}",name="GestionUser_false")
     */
    public function GestionUserfalse(UserRepository $userrepository, DemandeformateurRepository $demandeformateurrepository, $iduser)
    {
        $user = $userrepository->findOneBy(['id' => $iduser]);
        $demandesformateur = $demandeformateurrepository->findOneBy(['user' => $user]);
        $entityManager = $this->getDoctrine()->getManager();
        $user->setRole('user');
        $entityManager->remove($demandesformateur);
        $entityManager->flush();
        return $this->redirectToRoute('showformation');
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
