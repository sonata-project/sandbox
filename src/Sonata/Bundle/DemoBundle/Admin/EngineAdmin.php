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
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\MediaBundle\Form\Type\MediaType;

/**
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
final class EngineAdmin extends AbstractAdmin
{
    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('name')
            ->add('power')
            ->add('media');
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('name')
            ->add('power')
            ->add('media');
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('name')
            ->add('power');
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->add('name')
            ->add('power')
            ->add('media', MediaType::class, [
                 'provider' => 'sonata.media.provider.youtube',
                 'context' => 'default',
            ]);
    }
}
