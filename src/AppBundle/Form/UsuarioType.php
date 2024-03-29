<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Form;

use AppBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Formulario para crear y editar un usuario
 */
class UsuarioType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')

            ->add('password', RepeatedType::class, array(
                'type' => PasswordType::class,
                'invalid_message' => 'Las contraseñas no coinciden',
                'required' => true,
                'first_options'  => array('label' => 'Nueva contraseña'),
                'second_options' => array('label' => 'Repetir Contraseña'),
            ))
            ->add('nombre')
            ->add('apellido')
            ->add('telefono')
            ->add('email');

        if ($options['editar'] == true) {
            $builder->add('activo');
        }

        if ($options['editar'] == false) {
            if (in_array('ROLE_ADMIN', $options['role'])) {
                $builder
                    ->add('roles', ChoiceType::class, array(
                        'choices' => array(
                            'ROLE_ESPECIALISTA' => 'ROLE_ESPECIALISTA',
                            'ROLE_MANAGER' => 'ROLE_MANAGER',
                            'ROLE_ADMIN' => 'ROLE_ADMIN',
                        ),
                    ))->addModelTransformer(new CallbackTransformer(
                        function ($user) {
                            return $user->setRoles($user->getRoles()[0]);
                        },
                        function ($user) {
                            // transform the string back to an array
                            return $user->setRoles([$user->getRoles()]);
                        }
                    ));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'role' => ['ROLE_MANAGER'],
            'editar' => false,
        ]);
    }
}