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
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\Bundle\DemoBundle\Entity\Inspection;

/**
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class CarAdmin extends Admin
{
    /**
     * {@inheritdoc}
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('name')
            ->add('engine')
            ->add('rescueEngine')
            ->add('createdAt')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('engine')
            ->add('rescueEngine')
            ->add('createdAt')
            ->add('color')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
            ->add('engine')
            ->add('rescueEngine')
            ->add('createdAt')
            ->add('color')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('General', array('class' => 'col-md-6'))
                ->add('name')
                ->add('rescueEngine')
                ->add('createdAt')
            ->end()
            ->with('Options', array('class' => 'col-md-6'))
                ->add('engine', 'sonata_type_model_list')
                ->add('color', 'sonata_type_model_list')
            ->end()
            ->with('inspections', array('class' => 'col-md-12'))
                ->add('inspections', 'sonata_type_collection', array(
                    'by_reference'       => false,
                    'cascade_validation' => true,
                ), array(
                    'edit' => 'inline',
                    'inline' => 'table'
                ))
            ->end()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getNewInstance()
    {
        $object = parent::getNewInstance();

        $inspection = new Inspection();
        $inspection->setDate(new \DateTime());
        $inspection->setComment("Initial inspection");

        $object->addInspection($inspection);

        return $object;
    }
}
