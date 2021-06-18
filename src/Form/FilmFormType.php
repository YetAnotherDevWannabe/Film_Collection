<?php

namespace App\Form;

use App\Entity\Film;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Type;

class FilmFormType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('tmdbId', NumberType::class,
				[
					'label'       => 'ID themoviedb.org',
					'attr'        => ['placeholder' => '637649',],
					'constraints' =>
						[
							new NotBlank(['message' => 'Merci de renseigner un id themoviedb.org',]),
							new Length(
								[
									'min'        => 1,
									'max'        => 15,
									'minMessage' => 'L\'ID doit contenir au moins {{ limit }} chiffres',
									'maxMessage' => 'L\'ID doit contenir au maximum {{ limit }} chiffres',
								]),
						],
				])
			->add('name', TextType::class,
				[
					'label'       => 'Titre du film',
					'attr'        => ['placeholder' => 'Wrath of Man',],
					'constraints' =>
						[
							new NotBlank(['message' => 'Merci de renseigner un titre',]),
							new Length(
								[
									'min'        => 1,
									'max'        => 200,
									'minMessage' => 'Le titre du film doit contenir au moins {{ limit }} caractères',
									'maxMessage' => 'Le titre du film doit contenir au maximum {{ limit }} caractères',
								]),
						],
				])
			->add('year', NumberType::class,
				[
					'label'       => 'Année',
					'attr'        => ['placeholder' => '2021',],
					'constraints' =>
						[
							new NotBlank(['message' => 'Merci de renseigner l\'année de sortie du film',]),
							new Regex(
								[
									'pattern' => '/^(19[0-9]{2}|20[0-9]{2})$/',
									'message' => 'Année de sortie du film (entre 1900 et 2099)',
								]),
						],
				])
			->add('synopsis', TextareaType::class,
				[
					'label'       => 'Description',
					'attr'        =>
						[
							'rows' => 4,
							'placeholder' => 'A cold and mysterious new security guard for a Los Angeles cash truck company surprises his co-workers when he unleashes precision skills during a heist. The crew is left wondering who he is and where he came from. Soon, the marksman\'s ultimate motive becomes clear as he takes dramatic and irrevocable steps to settle a score.'
						],
					'constraints' =>
						[
							new NotBlank(['message' => 'Merci de renseigner un contenu',]),
							new Length(
								[
									'min'        => 1,
									'max'        => 20000,
									'minMessage' => 'La description doit contenir au moins {{ limit }} caractères',
									'maxMessage' => 'Le description doit contenir au maximum {{ limit }} caractères',
								]),
						],
				])
			->add('genre', TextType::class,
				[
					'label'       => 'Genre',
					'attr'        => ['placeholder' => 'Crime, Action'],
					'constraints' =>
						[
							new NotBlank(['message' => 'Merci de renseigner le ou les genres du film',]),
							new Length(
								[
									'min'        => 1,
									'max'        => 255,
									'minMessage' => 'Le genre doit contenir au moins {{ limit }} caractères',
									'maxMessage' => 'Le genre doit contenir au maximum {{ limit }} caractères',
								]),
						],
				])
			->add('note', PercentType::class, // https://symfony.com/doc/current/reference/forms/types/percent.html
				[
					'label'       => 'Note du film',
					'type'        => 'integer', // to be stored as int (0 to 100) in DB instead of fraction (0 to 1)
					'scale'       => 0,
					// TODO: '%' in twig page not correct height
					'attr'        =>
						[
							'maxlength'   => 3,
							'size'        => 3,
							'placeholder' => '78',
						],
					'constraints' =>
						[
							new Regex(
								[
									'pattern' => '/^([0-9]|[0-9]{2}|100)$/',
									'message' => 'Note entre 0% et 100%',
								]),
						],
				])
			->add('poster', UrlType::class,
				[
					'label'       => 'Poster',
					'attr'        => ['placeholder' => 'https://www.themoviedb.org/t/p/w600_and_h900_bestv2/o3thQMojEFxfeRWwH7UZmOqg8TS.jpg',],
					'constraints' =>
						[
							new Length(
								[
									'min'        => 1,
									'max'        => 255,
									'minMessage' => 'L\'URI doit contenir au moins {{ limit }} caractères',
									'maxMessage' => 'Le note doit contenir au maximum {{ limit }} caractères',
								]),
						],
				])
			->add('trailer', UrlType::class,
				[
					'label'       => 'Bande annonce',
					'attr'        => ['placeholder' => 'https://www.youtube.com/watch?v=EFYEni2gsK0',],
					'constraints' =>
						[
							new Length(
								[
									'min'        => 1,
									'max'        => 255,
									'minMessage' => 'L\'URL doit contenir au moins {{ limit }} caractères',
									'maxMessage' => 'Le note doit contenir au maximum {{ limit }} caractères',
								]),
						],
				])
			->add('save', SubmitType::class,
				[
					'label' => 'Ajouter film',
					'attr'  => ['class' => 'btn btn-block gradient-custom-reverse nav-btn-hover mx-0'],
				]
			);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => Film::class,
			// TODO: à enlever à la fin
			'attr'       => ['novalidate' => 'novalidate',],
		]);
	}
}
