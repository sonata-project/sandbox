<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Bundle\DemoBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class EngineChoiceTransformer implements DataTransformerInterface
{
    protected $choices;

    /**
     * @param array $choices
     */
    public function __construct(array $choices)
    {
        $this->choices = $choices;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        foreach ($this->choices as $pos => $choice) {
            if ($value == $choice) {
                return $pos;
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        if (!$value) {
            return null;
        }

        if (!isset($this->choices[$value])) {
            return null;
        }

        return $this->choices[$value];
    }
}