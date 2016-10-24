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

use AppBundle\Entity\Cliente;
use AppBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use AppBundle\Form\DataTransformer\StringToArrayTransformer;

/**
 * Defines the form used to create and manipulate blog posts.
 *
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
class UsuarioType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = new User();
        $builder
            ->add('nombre')
            ->add('apellido')
            ->add('telefono')
            ->add('email')
            ->add('activo')
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
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}