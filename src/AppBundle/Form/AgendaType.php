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

use AppBundle\Entity\Agenda;
use AppBundle\Entity\Servicio;
use AppBundle\Entity\Sesion;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Formulario para crear y editar agendas
 */
class AgendaType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('especialista', EntityType::class, array(
                'class' => 'AppBundle:User',
                'choice_label' => function ($cliente) {
                    return $cliente->getUsername() . ' - ' . $cliente->getNombre() . ' ' . $cliente->getApellido();
                },
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.roles = :roleManager')
                        ->setParameter('roleManager', '["ROLE_ESPECIALISTA"]')
                        ->andWhere('u.activo = true');
                },
            ))
            ->add('dia', DateType::class, ['data' => new \DateTime()])
            ->add('hora_inicio', TimeType::class, array(
                'input' => 'datetime',
                'widget' => 'choice',
            ))
            ->add('hora_final', TimeType::class, array(
                'input' => 'datetime',
                'widget' => 'choice',
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Agenda::class,
        ]);
    }
}