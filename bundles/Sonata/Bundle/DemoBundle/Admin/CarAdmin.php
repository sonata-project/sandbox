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
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Bundle\DemoBundle\Entity\Car;
use Sonata\Bundle\DemoBundle\Entity\Inspection;
use Sonata\Form\Type\CollectionType;
use Sonata\MediaBundle\Form\Type\MediaType;
use Symfony\Component\Validator\Constraints\Valid;

/**
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
final class CarAdmin extends AbstractAdmin
{
    public function getNewInstance(): Car
    {
        $car = parent::getNewInstance();

        $userAdmin = $this->getConfigurationPool()->getAdminByAdminCode('sonata.user.admin.user');

        $user = $userAdmin->getModelManager()->findOneBy($userAdmin->getClass(), [
            'username' => 'admin',
        ]);

        $inspection = new Inspection();
        $inspection->setDate(new \DateTime());
        $inspection->setComment('Initial inspection');
        $inspection->setInspector($user);

        $car->addInspection($inspection);

        return $car;
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('name')
            ->add('engine')
            ->add('rescueEngine')
            ->add('createdAt')
        ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('name')
            ->add('engine')
            ->add('rescueEngine')
            ->add('createdAt')
            ->add('color')
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('name')
            ->add('engine')
            ->add('rescueEngine')
            ->add('createdAt')
            ->add('color')
        ;
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->with('General', ['class' => 'col-md-5'])
                ->add('name')
                ->add('rescueEngine')
                ->add('createdAt')
            ->end()
            ->with('Options', ['class' => 'col-md-7'])
                ->add('engine', ModelListType::class)
                ->add('color', ModelListType::class)
                ->add('media', MediaType::class, [
                    'provider' => 'sonata.media.provider.image',
                    'context' => 'default',
                ])
            ->end()
            ->with('inspections', ['class' => 'col-md-12'])
                ->add('inspections', CollectionType::class, [
                    'by_reference' => false,
                    'constraints' => new Valid(),
                ], [
                    'edit' => 'inline',
                    'inline' => 'table',
                ])
            ->end()
        ;
    }
}
