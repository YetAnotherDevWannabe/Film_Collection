<?php

namespace App\Form;

use App\Entity\Collect;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class CollectFormType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('name', TextType::class, [
				'label'       => 'Nom de votre collection',
				'constraints' => [
					new NotBlank([
						'message' => 'Merci de renseigner un nom pour votre collection',
					]),
					new Length([
						'min'        => 1,
						'max'        => 150,
						'minMessage' => 'Le nom de votre collection au moins {{ limit }} caractères',
						'maxMessage' => 'Le nom de votre collection au maximum {{ limit }} caractères',
					]),
				],
			])
			->add('save', SubmitType::class,
				[
					'label' => 'Créer ma collection',
					'attr'  => ['class' => 'btn btn-block gradient-basecolor-reverse gradient-compliment-reverse mx-0'],
				]
			);;
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => Collect::class,
			// TODO: à enlever à la fin
			'attr'       => [
				'novalidate' => 'novalidate',
			],
		]);
	}
}
