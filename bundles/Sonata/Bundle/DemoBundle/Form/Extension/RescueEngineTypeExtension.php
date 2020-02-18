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

namespace Sonata\Bundle\DemoBundle\Form\Extension;

use Sonata\Bundle\DemoBundle\Entity\Engine;
use Sonata\Bundle\DemoBundle\Form\DataTransformer\EngineChoiceTransformer;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class RescueEngineTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        if (0 === \count($options['rescue_engines'])) {
            return;
        }

        $rescueEngine = $formBuilder->create('rescueEngine', ChoiceType::class, [
            'data_class' => Engine::class,
            'choices' => $this->getRescueEngines($options['rescue_engines']),
        ]);

        $rescueEngine->resetViewTransformers();
        $rescueEngine->addViewTransformer(new EngineChoiceTransformer($options['rescue_engines']));

        $formBuilder->add($rescueEngine);
    }

    public function getExtendedType(): string
    {
        return 'sonata_demo_form_type_car';
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'rescue_engines' => [],
            'data_class' => Engine::class,
        ]);
    }

    private function getRescueEngines(array $rescueEngines)
    {
        $choices = [];

        foreach ($rescueEngines as $pos => $engine) {
            $choices[$pos] = sprintf('%s - %s', $engine->getName(), $engine->getPower());
        }

        return $choices;
    }
}
