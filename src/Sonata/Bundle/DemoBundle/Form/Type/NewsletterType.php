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

/**
 * Class NewsletterType.
 *
 * This is the newsletter subscription/unsubscription form type
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class NewsletterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options)
    {
        $formBuilder
            ->add('email', 'email', [
                'attr' => [
                    'placeholder' => 'Email',
                ],
            ])
        ;
    }

    public function getName()
    {
        return 'sonata_demo_form_type_newsletter';
    }
}
