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
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserRegistrationType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('email', EmailType::class, [
				'label'       => 'Email',
				'constraints' => [
					new Email(['message' => 'L\'adresse email {{ value }} n\'est pas une adresse email valide.']),
					new NotBlank(['message' => 'L\'adresse email ne peut pas être vide.']),
				],
				'label_attr'  => ['help' => ' : champs obligatoires',],
			])
			->add('plainPassword', RepeatedType::class, [
				'mapped'          => false,
				'type'            => PasswordType::class,
				'invalid_message' => 'La confirmation  ne correspond pas au mot de passe',
				'first_options'   => ['label' => 'Mot de passe'],
				'second_options'  => ['label' => 'Confirmation du mot de passe'],
				'constraints'     => [
					new NotBlank([
						'message' => 'Merci de renseigner un mot de passe',
					]),
					new Regex([
						'pattern' => '/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*[ !"#\$%&\'()*+,\-.\/:;<=>?@[\\\\\]\^_`{\|}~]).{8,4096}$/',
						'message' => 'Votre mot de passe doit contenir au moins 8 caractères a-z, A-A, 0-9 un caractère spécial',
					]),
				],

			])
			->add('nickname', TextType::class, [
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
			])
			->add('avatar', FileType::class, [
				'label'       => 'Avatar',
				'attr'        => ['accept' => 'image/jpeg, image/jpg, image/png',],
				'constraints' => [
					new File([
						'maxSize'          => '1M',
						'mimeTypes'        => [
							'image/jpeg',
							'image/jpg',
							'image/png',
						],
						'mimeTypesMessage' => 'Votre image doit être au format JPG, PNG ou JPEG',
						'maxSizeMessage'   => 'Ce fichier de {{ size }}{{ suffix }} est trop lourd, l\'image demandée doit faire au maximum {{ limit }}{{ suffix }}',
					]),
				],
			])
			->add('save', SubmitType::class, [
				'label' => 'Créer mon compte',
				'attr'  => [
					'class'                    => 'btn btn-block gradient-basecolor-reverse gradient-compliment-reverse mx-0',
					'data-mdb-ripple-duration' => '0ms',
				],
			]);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => User::class,
			'attr'       => ['novalidate' => 'novalidate'],
		]);
	}
}
