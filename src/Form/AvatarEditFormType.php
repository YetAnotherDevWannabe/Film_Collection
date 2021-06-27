<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\File;

class AvatarEditFormType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('avatar', FileType::class, [
				'label'       => 'Ajouter un avatar',
				'attr'        => ['accept' => 'image/jpeg, image/png',],
				'constraints' =>
					[
						new NotBlank([
							'message' => 'Vous devez sélectionner un fichier',
						]),
						new File([
							'maxSize'          => '1M',
							'mimeTypes'        => ['image/jpeg', 'image/png',],
							'mimeTypesMessage' => 'Votre image doit être de type JPG ou PNG',
							'maxSizeMessage'   => 'Fichier trop volumineux ({{ size }}{{ suffix }}). La taille maximum est {{ limit }}{{ suffix }}',
						]),
					],
			])
			->add('save', SubmitType::class, [
				'label' => 'Valider',
				'attr'  => [
					'class'                    => 'btn btn-block stop-mdb text-complimain text-maincolor-hover gradient-basecolor-reverse gradient-compliment-reverse mx-0 my-4',
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
