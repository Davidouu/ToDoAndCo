<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', null, [
                'label'    => 'Nom d\'utilisateur',
                'required' => true,
                'attr'     => ['class' => 'form-control'],
            ])
            ->add('password', RepeatedType::class, [
                'type'           => PasswordType::class,
                'required'       => true,
                'first_options'  => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Tapez le mot de passe à nouveau'],
                'options'        => ['attr' => ['class' => 'form-control']],
            ])
            ->add('email', EmailType::class, [
                'label'    => 'Adresse email',
                'required' => true,
                'attr'     => ['class' => 'form-control'],
            ])
            ->add('roles', ChoiceType::class, [
                'label'    => 'Rôle',
                'required' => true,
                'choices'  => [
                    'Utilisateur'    => 'ROLE_USER',
                    'Administrateur' => 'ROLE_ADMIN',
                ],
                'multiple' => true,
                'attr'     => ['class' => 'form-control'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
