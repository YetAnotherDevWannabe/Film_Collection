<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CollectDeleteFormType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('confirm', SubmitType::class,
				[
					'label_html' => true,
					'label'      => '<i class="fas fa-check me-2"></i>Confirmer la suppression',
					'attr'       => ['class' => 'btn btn-block btn-lg btn-success',],
				])
			->add('cancel', SubmitType::class,
				[
					'label_html' => true,
					'label'      => '<i class="fas fa-chevron-left"></i><i class="fas fa-chevron-left me-2"></i>Annuler la suppression',
					'attr'       => ['class' => 'btn btn-block btn-lg btn-danger',],
				]);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			// Configure your form options here
		]);
	}
}
