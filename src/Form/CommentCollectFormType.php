<?php

namespace App\Form;

use App\Entity\Collect;

use App\Entity\CommentCollect;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class CommentCollectFormType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('content', TextareaType::class, [
				'label'       => false,
				'purify_html' => true,
				'attr'        => [
					'placeholder' => 'Laissez votre commentaire...',
					'rows'        => 10,
				],
				'constraints' => [
					new NotBlank([
						'message' => 'Merci de renseigner un contenu',
					]),
					new Length([
						'min'        => 1,
						'max'        => 2000,
						'minMessage' => 'Le commentaire doit contenir au moins {{ limit }} caractères',
						'maxMessage' => 'Le commentaire doit contenir au maximum {{ limit }} caractères',
					]),
				],
			])
			->add('save', SubmitType::class, [
				'label' => 'Publier',
				'attr'  => [
					'class'                    => 'gradient-basecolor-reverse gradient-compliment-reverse col-12',
					'data-mdb-ripple-duration' => '0ms',
				],
			]);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => CommentCollect::class,
			'attr'       => [
				'novalidate' => 'novalidate',
			],
		]);
	}
}
