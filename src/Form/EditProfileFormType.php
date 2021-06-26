<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class EditProfileFormType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('email', EmailType::class, [
				'label'       => 'Email',
				'required'    => false,
				'constraints' => [
					new Email([
						'message' => 'L\'adresse courriel {{ value }} est invalide',
					]),
				],
			])
			->add('nickname', TextType::class, [
				'label'       => "Nom d'utilisateur",
				'required'    => false,
				'constraints' => [
					new Length([
						'min'        => 2,
						'max'        => 50,
						'minMessage' => 'Votre pseudonyme doit contenir au minimum {{ limit }} caractères',
						'maxMessage' => 'Votre pseudonyme doit contenir au maximum {{ limit }} caractères',
					]),
				],
			])
			->add('oldPassword', PasswordType::class, [
				'label'       => 'Vérification du mot de passe',
				'required'    => false,
				'constraints' => [
					new Regex([
						'pattern' => '/(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[ !\"\#\$%&\'\(\)*+,\-.\/:;<=>?@[\\^\]_`\{|\}~])^.{8,4096}$/',
						'message' => 'Votre mot de passe n\'est pas conforme',
					]),
				],
			])
			->add('confirm', SubmitType::class,
				[
					'label_html' => true,
					'label' => '<i class="fas fa-check me-2"></i>Enregistrer',
					'attr'  => [
						// 'class'                    => 'btn btn-block gradient-basecolor-reverse gradient-compliment-reverse mx-0 mb-4',
						'class'                    => 'btn btn-block btn-success mx-0 mb-4',
						'data-mdb-ripple-duration' => '0',
					],
				])
			->add('cancel', SubmitType::class,
				[
					'label_html' => true,
					'label'      => '<i class="fas fa-chevron-left"></i><i class="fas fa-chevron-left me-2"></i>Annuler',
					'attr'       => [
						'class'                    => 'btn btn-block btn-danger',
						'data-mdb-ripple-duration' => '0',
					],
				]);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'attr' => [
				'novalidate' => 'novalidate',
			],
		]);
	}
}
