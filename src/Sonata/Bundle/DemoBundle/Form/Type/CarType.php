<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Bundle\DemoBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class CarType extends AbstractType
{
    public function buildForm(FormBuilder $formBuilder, array $options)
    {
        $formBuilder
            ->add('createdAt', 'datetime')
            ->add('name')
            ->add('engine', 'sonata_demo_form_type_engine', array(
                'data_class' => 'Sonata\Bundle\DemoBundle\Model\Engine'
            ))
        ;
    }

    public function getName()
    {
        return 'sonata_demo_car';
    }

    public function getDefaultOptions(array $options)
    {
        $options['data_class'] = 'Sonata\Bundle\DemoBundle\Model\Car';

        return $options;
    }
}