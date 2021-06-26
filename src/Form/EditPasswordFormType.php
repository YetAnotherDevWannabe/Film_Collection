<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class EditPasswordFormType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('oldPassword', PasswordType::class, [
				'label'           => 'Ancien mot de passe',
				'invalid_message' => 'La confirmation ne correspond pas votre mot de passe',
				'constraints'     => [
					new NotBlank([
						'message' => 'Merci de renseigner votre ancien mot de passe',
					]),
					new Regex([
						'pattern' => '/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*[ !"#\$%&\'()*+,\-.\/:;<=>?@[\\\\\]\^_`{\|}~]).{8,4096}$/',
						'message' => 'Votre mot de passe n\'est pas conforme',
					]),
				],
			])
			->add('newPassword', RepeatedType::class, [
				'mapped'          => false,
				'type'            => PasswordType::class,
				'invalid_message' => 'La confirmation  ne correspond pas au mot de passe',
				'first_options'   => ['label' => 'Nouveau mot de passe'],
				'second_options'  => ['label' => 'Confirmation du nouveau mot de passe'],
				'constraints'     => [
					new NotBlank([
						'message' => 'Merci de renseigner un nouveau mot de passe',
					]),
					new Regex([
						'pattern' => '/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*[ !"#\$%&\'()*+,\-.\/:;<=>?@[\\\\\]\^_`{\|}~]).{8,4096}$/',
						'message' => 'Votre mot de passe n\'est pas conforme',
					]),
				],
			])
			->add('save', SubmitType::class, [
				'label' => 'Enregistrer',
				'attr'  => [
					'class'                    => 'btn btn-block gradient-basecolor-reverse gradient-compliment-reverse mx-0 mb-4',
					'data-mdb-ripple-duration' => '0',
				],
			]);
		;
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'attr'       => [
				'novalidate' => 'novalidate',
			],
		]);
	}
}
