<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use App\Entity\UpdatePassword;
use App\Form\RegistrationType;
use App\Form\PasswordUpdateType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{
    /**
     * @Route("/login", name="account_login")
     */
    public function login(AuthenticationUtils $utils)
    {
        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();

        return $this->render('account/login.html.twig', [
            'hasError' => $error !== null,
            'username' => $username
        ]);
    }

    /**
     * Permet de se deconnecter
     * @Route("/logout", name="account_logout")
     *
     * @return void
     */
    public function logout(){
        // Rien...
    }

    /**
     * @Route("/register", name="account_register")
     *
     * @return void
     */
    public function register(Request $request, ManagerRegistry $managerRegistry, UserPasswordEncoderInterface $encoder) {
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $hash = $encoder->encodePassword($user, $user->getHash());
            $user->setHash($hash);

            $em = $managerRegistry->getManager();
            $em->persist($user);
            $em->flush();
        }

        return $this->render('/account/registration.html.twig', [
            'form' => $form->createView()
        ]);

    }

    /**
     * Permet de modifier les données d'utilisateur
     * 
     * @Route("/account/profile", name="account_profile")
     *
     * @return Response
     */
    public function profile(Request $request, ManagerRegistry $managerRegistry){

        $user = $this->getUser();

        $form = $this->createForm(AccountType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $em = $managerRegistry->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash(
                'success',
                "les données ont bien été modifiées !"
            ) ;

        }

        return $this->render('/account/profile.html.twig', [
            'form' => $form->createView()
        ]);

    }

    /**
     * Permet de modifier le mot de passe utilisateur
     * @Route("/account/update-password", name="account_update_password")
     *
     * @return void
     */
    public function updatePassword(Request $request, ManagerRegistry $managerRegistry, UserPasswordEncoderInterface $encoder){
        $passwordUpdate = new UpdatePassword();
        $user = $this->getUser();

        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            // Verifier que le oldPassword soit le meme que le password de l'user
            if(!password_verify($passwordUpdate->getOldPassword(), $user->getHash())) {
                //Gerer l'erreur
                $form->get('oldPassword')->addError(new FormError("Vous n'avez pas tapé votre ancien mot de passe"));
            } else {
                $newPassword = $passwordUpdate->getNewPassword();
                $hash = $encoder->encodePassword($user, $newPassword);

                $user->setHash($hash);

                $em = $managerRegistry->getManager();
                $em->persist($user);
                $em->flush();

                $this->addFlash(
                    'success',
                    "le mot de passe a bien été modifié !"
                ) ;

                return $this->redirectToRoute('homepage');
            }
        }

        return $this->render('/account/updatepassword.html.twig', [
            'form' => $form->createView()
        ]);

    }

    /**
     * Permet d'afficher le profile de l'utilisateur connecté
     * 
     * @Route("/account", name="account_index")
     *
     * @return Response
     */
    public function myAccount () {
        return $this->render('user/index.html.twig', [
            'user' => $this->getUser()
        ]);
    }
}
