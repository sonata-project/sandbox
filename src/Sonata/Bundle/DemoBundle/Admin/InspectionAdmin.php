<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Bundle\DemoBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class InspectionAdmin extends Admin
{
    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        $filter
            ->add('date')
            ->add('car')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('date')
            ->add('comment')
            ->add('car')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        if (!$this->hasParentFieldDescription()) {
            $formMapper->add('car', null, array('constraints' => new Assert\NotNull()));
        }

        if ($this->hasSubject() && $this->getSubject()->getStatus() == 1) {
            $choices = array(
                1 => 'Current value is 1',
                2 => 'switch to 2',
            );
        } else {
            $choices = array(
                2 => 'current value is 2',
                1 => 'switch to 1',
            );
        }

        $formMapper->add('status', 'choice', array('choices' => $choices));

        $formMapper->add('comment', 'sonata_simple_formatter_type', array(
            'format' => 'richhtml',
        ));

        $formMapper->add('date', null, array('widget' => 'single_text'));
        $formMapper->add('inspector', 'sonata_type_model_autocomplete', array(
            'property' => 'username',
        ));
    }
}
