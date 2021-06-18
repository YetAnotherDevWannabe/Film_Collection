<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('nickname', TextType::class,
				[
					'label'       => 'Nom d\'utilisateur',
					'constraints' =>
						[
							new NotBlank(['message' => 'Merci de renseigner un nom d\'utilisateur']),
							new Length(
								[
									'min'        => '2',
									'max'        => '50',
									'minMessage' => 'Votre nom d\'utilisateur doit contenir au moins {{ limit }} caractères.',
									'maxMessage' => 'Votre nom d\'utilisateur doit contenir au maximum {{ limit }} caractères.',
								]
							),
						],
					// In case of error 'Expected argument of type "string", "null" given at property path "username".'
					// 'empty_data' => '',
				]
			)
			->add('email', EmailType::class,
				[
					'label'       => 'Adresse email',
					'constraints' =>
						[
							new Email(['message' => 'L\'adresse email {{ value }} n\'est pas une adresse email valide.']),
							new NotBlank(['message' => 'L\'adresse email ne peut pas être vide.']),
						],
				]
			)
			->add('plainPassword', RepeatedType::class,
				[
					'mapped'          => false,
					'type'            => PasswordType::class,
					'invalid_message' => 'La confirmation du mot de passe ne correspond pas.',
					'first_options'   => ['label' => 'Mot de passe'],
					'second_options'  => ['label' => 'Confirmation du mot de passe'],
					// 'attr'        => ['autocomplete' => 'new-password'],
					'constraints'     =>
						[
							new NotBlank(['message' => 'Merci de renseigner un mot de passe']),
							new Regex(
								[
									'pattern' => '/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*[ !"#\$%&\'()*+,\-.\/:;<=>?@[\\\\\]\^_`{\|}~]).{8,4096}$/',
									'message' => 'Votre mot de passe doit contenir au minimum 8 caractères, et obligatoirement une minuscule, une majuscule, un chiffre et un caractère spécial',
								]),
						],
				])
			->add('save', SubmitType::class,
				[
					'label' => 'Créer mon compte',
					'attr'  => ['class' => 'btn btn-block gradient-custom-reverse nav-btn-hover mx-0'],
				]
			);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => User::class,
			'attr' => ['novalidate' => 'novalidate']
		]);
	}
}
