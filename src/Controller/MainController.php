<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\DeleteAvatarFormType;
use App\Form\EditAvatarFormType;
use App\Form\ProfilDeleteFormType;
use App\Form\ProfilEditFormType;
use App\Form\RegistrationFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Form\EditProfileFormType;

class MainController extends AbstractController
{
	/**
	 * Controller for the home page
	 * @Route("/", name="main_home")
	 */
	public function home(): Response
	{
		// $this->addFlash('error', 'error');
		// $this->addFlash('warning', 'warning');
		// $this->addFlash('info', 'info');
		// $this->addFlash('success', 'success');

		return $this->render('main/index.html.twig');
	}


	/**
	 * Controller for the profil page
	 * @Route("/profil/", name="main_profil")
	 */
	public function profil(): Response
	{
		return $this->render('main/profil.html.twig');
	}


	/**
     * @Route("/profil/edit/", name="main_profil_edit")
     */
    public function profilEdit(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {

        //This is a form to modify data
        $editDataForm = $this->createForm(EditProfileFormType::class);

        //We fill the form with  POST data obtained from $request object
        $editDataForm->handleRequest($request);

        //Conditions if the form was succesffully filled in
        if($editDataForm->isSubmitted()){
            if($editDataForm->isValid()){


                $user = $this->getUser();

                /*$user
                    ->setNickname($editDataForm->get('nickname')->getData())
                    ->setEmail($editDataForm->get('email')->getData())
					->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $editDataForm->get('password')->getData()
                    )
                )
                ;*/

				//We test below if any field is not blank, we set the new data filled in by the current connected user

				$emailField = $editDataForm->get('email');

				if(!$emailField->isEmpty()){

					$user->setEmail( $emailField->getData() );

				}

				$nicknameField = $editDataForm->get('nickname');

				if(!$nicknameField->isEmpty()) {
					$user->setNickname($nicknameField->getData());

				}

				$passwordField = $editDataForm->get('password');

				if(!$passwordField->isEmpty()){
					$user->setPassword(
						$passwordEncoder->encodePassword(
							$user,
							$editDataForm->get('password')->getData()
						)
					)
					;

				}

                //We use the entity manager to save changes
                $em = $this->getDoctrine()->getManager();

                $em->flush();

				$em->refresh($user);

                //Success message when the user successfully edited at least one field
				if(!$emailField->isEmpty() || !$nicknameField->isEmpty() || !$passwordField->isEmpty()){
					$this->addFlash('success', 'Vos modifications ont bien été prises en compte');
				}

            }

        }

        return $this->render('main/edit.html.twig', [
            'editDataForm' => $editDataForm->createView(),
        ]);
    }


	/**
	 * Controller for the profil suppression page
	 * @Route("/profil/delete/", name="main_profil_delete")
	 */
	public function profilDelete(): Response
	{
		$user = $this->getUser();

		$user
			->setActive(false);

		$em = $this->getDoctrine()->getManager();
		$em->flush();

		$this->addFlash('success', 'Votre compte a bien été supprimé');

		return $this->render('main/index.html.twig');
	}


	/**
	 * Controller for the avatar edition page
	 * @Route("/profil/avatar/edit/", name="main_avatar_edit")
	 */
	public function avatarEdit(): Response
	{
		return $this->render('main/profil-edit-avatar.html.twig');
	}


	/**
	 * Controller for the avatar suppression page
	 * @Route("/profil/avatar/delete/", name="main_avatar_delete")
	 */
	public function avatarDelete(): Response
	{
		return $this->render('main/profil-delete-avatar.html.twig');
	}
}
