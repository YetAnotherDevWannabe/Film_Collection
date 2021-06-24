<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class DisableAccountFormType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('confirm', SubmitType::class, [
				'label_html' => true,
				'label'      => '<i class="fas fa-check me-2"></i>Confirmer la suppression du compte',
				'attr'       => [
					'class'                    => 'btn btn-block btn-lg btn-success',
					'data-mdb-ripple-duration' => '0ms',
				],
			])
			->add('cancel', SubmitType::class, [
				'label_html' => true,
				'label'      => '<i class="fas fa-chevron-left"></i><i class="fas fa-chevron-left me-2"></i>Annuler la suppression du compte',
				'attr'       => [
					'class'                    => 'btn btn-block btn-lg btn-warning',
					'data-mdb-ripple-duration' => '0ms',
				],
			]);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			// Configure your form options here
		]);
	}
}
