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

namespace Sonata\Bundle\DemoBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

final class EngineChoiceTransformer implements DataTransformerInterface
{
    private $choices;

    public function __construct(array $choices)
    {
        $this->choices = $choices;
    }

    public function transform($value)
    {
        foreach ($this->choices as $pos => $choice) {
            if ($value === $choice) {
                return $pos;
            }
        }

        return null;
    }

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
