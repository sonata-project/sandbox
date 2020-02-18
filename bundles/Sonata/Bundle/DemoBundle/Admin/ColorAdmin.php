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

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\AdminType;
use Sonata\AdminBundle\Show\ShowMapper;

/**
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
final class ColorAdmin extends AbstractAdmin
{
    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('r')
            ->add('g')
            ->add('b')
            ->add('material')
            ->add('enabled')
        ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('r')
            ->addIdentifier('g')
            ->addIdentifier('b')
            ->add('material')
            ->add('enabled')
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('r')
            ->add('g')
            ->add('b')
            ->add('material')
            ->add('enabled')
        ;
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->add('r')
            ->add('g')
            ->add('b')
            ->add('material', AdminType::class)
            ->add('enabled')
        ;
    }
}
