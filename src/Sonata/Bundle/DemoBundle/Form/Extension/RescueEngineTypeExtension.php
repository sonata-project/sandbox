<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Bundle\DemoBundle\Form\Extension;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractTypeExtension;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Sonata\Bundle\DemoBundle\Form\DataTransformer\EngineChoiceTransformer;

class RescueEngineTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $formBuilder, array $options)
    {
        if (count($options['rescue_engines']) == 0) {
            return;
        }

        $rescueEngine = $formBuilder->create('rescueEngine', 'choice', array(
            'data_class' => 'Sonata\Bundle\DemoBundle\Entity\Engine',
            'choices' => $this->getRescueEngines($options['rescue_engines']),
        ));

        $rescueEngine->resetViewTransformers();
        $rescueEngine->addViewTransformer(new EngineChoiceTransformer($options['rescue_engines']));

        $formBuilder->add($rescueEngine);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return 'sonata_demo_form_type_car';
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'rescue_engines' => array(),
            'data_class'     => 'Sonata\Bundle\DemoBundle\Entity\Engine',
        ));
    }

    /**
     * {@inheritdoc}
     */
    private function getRescueEngines(array $rescueEngines)
    {
        $choices = array();

        foreach ($rescueEngines as $pos => $engine) {
            $choices[$pos] = sprintf('%s - %s', $engine->getName(), $engine->getPower());
        }

        return $choices;
    }
}