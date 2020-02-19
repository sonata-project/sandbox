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

namespace Sonata\Bundle\DemoBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class InspectionAdmin extends Admin
{
    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        $filter
            ->add('date')
            ->add('car')
        ;
    }

    protected function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('date')
            ->add('comment')
            ->add('car')
        ;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        if (!$this->hasParentFieldDescription()) {
            $formMapper->add('car', null, ['constraints' => new Assert\NotNull()]);
        }

        if ($this->hasSubject() && 1 === $this->getSubject()->getStatus()) {
            $choices = [
                1 => 'Current value is 1',
                2 => 'switch to 2',
            ];
        } else {
            $choices = [
                2 => 'current value is 2',
                1 => 'switch to 1',
            ];
        }

        $formMapper->add('status', 'choice', ['choices' => $choices]);

        $formMapper->add('comment', 'sonata_simple_formatter_type', [
            'format' => 'richhtml',
        ]);

        $formMapper->add('date', null, ['widget' => 'single_text']);
        $formMapper->add('inspector', 'sonata_type_model_autocomplete', [
            'property' => 'username',
        ]);
    }
}
