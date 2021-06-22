<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use \DateTime;
use App\Entity\User;
use App\Form\UserRegistrationType;
use App\Recaptcha\RecaptchaValidator;
use Symfony\Component\Form\FormError;
use Symfony\Component\String\Slugger\SluggerInterface;


class RegistrationController extends AbstractController
{
    /**
     * @Route("/inscription/", name="main_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, RecaptchaValidator $recaptcha, SluggerInterface $slugger): Response
    {
        //This is the redirection of the user to homepage if he is already connected
        if($this->getUser()){
            return redirectToRoute('main_home');
        }

        //We create a new user
        $user = new User();

        //This is a form linked to this new user
        $registrationForm = $this->createForm(UserRegistrationType::class, $user);

        //We fill the form with  POST data obtained from $request object
        $registrationForm->handleRequest($request);

        //Conditions if the form was succesffully filled in
        if($registrationForm->isSubmitted()){

            //Verification of the recaptcha validity
            if(!$recaptcha->verify( $request->request->get('g-recaptcha-response'), $request->server->get('REMOTE_ADDR') )){
                //Inclusion of an error message if the recaptcha is empty
                $registrationForm->addError(new formError('Veuillez valider le recaptcha s\'il-vous-plaît.'));
            }



            if($registrationForm->isValid()){

                //We get the avatar field
                $avatar = $registrationForm->get('avatar')->getData();

                //Condition if the avatar field is used, since it is not required
                if($avatar){
                    $originalFileName = pathinfo($avatar->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFileName = $slugger->slug($originalFileName);
                    $newFileName = $safeFileName . '-' . uniqid() . '.' . $avatar->guessExtension();

                    $avatar->move(
                        $this->getParameter('users_uploaded_avatar_dir'),
                        $newFileName
                    );
                }

                //We fill in User table with encoded password, registration date and avatar
                $user
                ->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $registrationForm->get('plainPassword')->getData()
                    )
                )
                ->setActive(true)

                ->setRegistrationDate(new DateTime())
                ;

                if($avatar){
                    $user
                    ->setAvatar($newFileName);
                }


                //We use the entity manager to save the new user in the database
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                //Succes message when the account has been created and the user has been registered
                $this->addFlash('success', 'Votre compte a bien été bien créé');


                //We redirect the user to the login page
                return $this->redirectToRoute('main_home');
            }
        }


        //We display the view with the "render" function and generate the form view

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $registrationForm->createView()
        ]);
    }
}
