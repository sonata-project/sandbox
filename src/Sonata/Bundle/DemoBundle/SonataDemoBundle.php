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

namespace Sonata\Bundle\DemoBundle;

use Sonata\Bundle\DemoBundle\Form\Type\NewsletterType;
use Sonata\CoreBundle\Form\FormHelper;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SonataDemoBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        FormHelper::registerFormTypeMapping([
            'sonata_demo_form_type_newsletter' => NewsletterType::class,
        ]);
    }
}
