<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Bundle\DemoBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options)
    {
        $formBuilder
            ->add('createdAt', 'datetime')
            ->add('name')
            ->add('engine', 'sonata_demo_form_type_engine', [
                'data_class' => 'Sonata\Bundle\DemoBundle\Entity\Engine',
            ])
        ;
    }

    public function getName()
    {
        return 'sonata_demo_form_type_car';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Sonata\Bundle\DemoBundle\Entity\Car',
            'rescue_engines' => [],
        ]);
    }
}
