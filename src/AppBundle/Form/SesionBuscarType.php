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
 * Formulario para filtrar las sesiones
 */
class SesionBuscarType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('filtro', ChoiceType::class, array(
                'choices' => array(
                    'Todas' => 'todo',
                    'Sesiones  ejecutadas pero sin pagar.' => 'ejecutadas',
                    'Sesiones retrasadas.' => 'retrasadaa',
                    'Sesiones sin agendar.' => 'sinAgendar',
                    'Sesiones ejecutadas y pagadas.' => 'ejecutadasPagadas',
                ),
                'multiple' => false,
                'expanded' => true,
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {

    }
}