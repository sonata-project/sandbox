<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Sonata\Bundle\DemoBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;


/**
 * Class Builder
 *
 * @package Sonata\Bundle\DemoBundle\Menu
 *
 * @author Hugo Briand <briand@ekino.com>
 */
class Builder extends ContainerAware
{
    /**
     * Creates the header menu
     *
     * @param FactoryInterface $factory
     * @param array            $options
     *
     * @return \Knp\Menu\ItemInterface
     */
    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $menuOptions = array_merge($options, array(
            'childrenAttributes' => array('class' => 'nav nav-pills'),
        ));

        $menu = $factory->createItem('main', $menuOptions);

        $menu->addChild('News', array('route' => 'sonata_news_home'));
        $menu->addChild('Shop', array('route' => 'sonata_category_index'));

        $extras = $factory->createItem('extras', array(
            'uri' => "#",
            'attributes' => array('class' => 'dropdown'),
            'childrenAttributes' => array('class' => 'dropdown-menu'),
            'linkAttributes' => array('class' => 'dropdown-toggle', 'data-toggle' => 'dropdown'),
            'label' => 'Extras <b class="caret caret-menu"></b>',
            'extras' => array(
                'safe_label' => true,
            )
        ));

        $extras->addChild('Gallery', array('route' => 'sonata_media_gallery_index'));
        $extras->addChild('Media & SEO', array('route' => 'sonata_demo_media'));

        $menu->addChild($extras);

        $menu->addChild('Admin', array('route' => 'sonata_admin_redirect'));

        return $menu;
    }
}