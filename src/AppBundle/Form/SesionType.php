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

use AppBundle\Entity\Servicio;
use AppBundle\Entity\Sesion;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Formulario para crear y editar sesiones
 */
class SesionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (in_array('ROLE_ADMIN', $options['role']) || ($options['tipo'] == 'crear')) {
            $builder
                ->add('manager', EntityType::class, array(
                    'class' => 'AppBundle:User',
                    'choice_label' => function ($cliente) {
                        return $cliente->getUsername() . ' - ' . $cliente->getNombre() . ' ' . $cliente->getApellido();
                    },
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('u')
                            ->where('u.roles = :roleManager')
                            ->orWhere('u.roles = :roleAdmin')
                            ->andWhere('u.activo = true')
                            ->setParameter('roleManager', '["ROLE_MANAGER"]')
                            ->setParameter('roleAdmin', '["ROLE_ADMIN"]');
                    },
                ));
            if ( ! $options['agenda']) {
                $builder->add('servicio', EntityType::class, array(
                    'class' => 'AppBundle:Servicio',
                    'choice_label' => 'nombre',
                ));
            }
            $builder->add('cliente', EntityType::class, array(
                'class' => 'AppBundle:Cliente',
                'choice_value' => 'cedula',
                'choice_label' => function ($cliente) {
                    return $cliente->getNombre() . ' ' . $cliente->getApellido() . ' - ' . $cliente->getCedula();
                }
            ));
        }
        if ((in_array('ROLE_ESPECIALISTA', $options['role']) || in_array('ROLE_MANAGER', $options['role']) || in_array('ROLE_ADMIN', $options['role'])) && ($options['tipo'] != 'crear')) {
            $builder->add('checkin');
        }
        if ((in_array('ROLE_MANAGER', $options['role']) || in_array('ROLE_ADMIN', $options['role'])) && ($options['tipo'] != 'crear')) {
            $builder->add('ejecutada');
        }
        if (in_array('ROLE_MANAGER', $options['role']) || in_array('ROLE_ADMIN', $options['role']) || ($options['tipo'] == 'crear')) {
            $builder->add('cancelada');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sesion::class,
            'role' => ['ROLE_ESPECIALISTA'],
            'agenda' => false,
            'tipo' => ''
        ]);
    }
}