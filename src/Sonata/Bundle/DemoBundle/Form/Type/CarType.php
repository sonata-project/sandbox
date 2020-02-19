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

use Sonata\Bundle\DemoBundle\Entity\Car;
use Sonata\Bundle\DemoBundle\Entity\Engine;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add('createdAt', DateTimeType::class)
            ->add('name')
            ->add('engine', EngineType::class, [
                'data_class' => Engine::class,
            ])
        ;
    }

    public function getName(): string
    {
        return 'sonata_demo_form_type_car';
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Car::class,
            'rescue_engines' => [],
        ]);
    }
}
