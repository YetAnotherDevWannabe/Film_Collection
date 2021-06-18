<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class EditAvatarFormType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('avatar', FileType::class,
				[
					'label'       => 'Sélectionnez un avatar',
					'constraints' =>
						[
							new File(
								[
									'maxSize'          => '1M',
									'mimeTypes'        => ['image/jpeg', 'image/png', 'image/gif',],
									'mimeTypesMessage' => 'L\'image doit être de type jpg, png ou gif',
									'maxSizeMessage'   => 'Fichier trop volumineux ({{ size }}{{ suffix }}). Maximum autorisée : {{ limit }}{{ suffix }}',
								]),
							new NotBlank(['message' => 'Merci de sélectionner un fichier',]),
						],
				])
			->add('save', SubmitType::class,
				[
					'label' => 'Changer l\'avatar',
					'attr'  =>
						[
							'class' => 'btn btn-block gradient-custom-reverse nav-btn-hover mx-0',
						],
				]);
	}


	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'attr' => ['novalidate' => 'novalidate']
		]);
	}
}
