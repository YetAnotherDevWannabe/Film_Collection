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

class EditProfileFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Adresse courriel',
                'required' => false,
                'constraints' => [
                    new Email([
                        'message' => 'L\'adresse courriel {{ value }} est invalide'
                    ]),
                ],
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'required' => false,
                'constraints' => [
                    new Regex([
                        'pattern' => '/(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[ !\"\#\$%&\'\(\)*+,\-.\/:;<=>?@[\\^\]_`\{|\}~])^.{8,4096}$/',
                        'message' => 'Votre mot de passe doit contenir au moins 8 caractères dont une minuscule, une majuscule, un caractère spécial, un chiffre'
                    ])
                ],

            ])
            ->add('nickname', TextType::class, [
                'label' => "Pseudonyme",
                'required' => false,
                'constraints' => [
                    new Length([
                        'min' => 2,
                        'max' => 150,
                        'minMessage' => 'Votre pseudonyme doit contenir au minimum {{ limit }} caractères',
                        'maxMessage' => 'Votre pseudonyme doit contenir au maximum {{ limit }} caractères',
                    ]),
                ],
            ])
            /*->add('avatar', FileType::class, [
                'label' => 'Avatar',
                'attr' => [
                    'accept' => 'image/jpeg, image/jpg, image/png',
                ],
                'constraints' => [

                    new File([
                        'maxSize' => '1M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/jpg',
                            'image/png'
                        ],
                        'mimeTypesMessage' => 'Votre image doit être au format JPG, PNG ou JPEG',
                        'maxSizeMessage' => 'Ce fichier de {{ size }}{{ suffix }}est trop lourd, l\'image demandée doit faire au maximum {{ size }}{{ suffix }}',
                    ])
                ],
            ])*/
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => [
                    'class' => 'btn btn-primary col-12 mt-3'
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([

        ]);
    }
}
