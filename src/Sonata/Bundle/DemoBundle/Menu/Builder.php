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
        $isFooter = array_key_exists('is_footer', $options) ? $options['is_footer'] : false;

        $shopCategories = $this->container->get('sonata.classification.manager.category')->findBy(array('enabled' => true, 'parent' => null));

        $menuOptions = array_merge($options, array(
            'childrenAttributes' => array('class' => 'nav nav-pills'),
        ));

        $menu = $factory->createItem('main', $menuOptions);

        $menu->addChild('News', array('route' => 'sonata_news_home'));

        $shopMenuParams = array('route' => 'sonata_category_index');

        if (count($shopCategories) > 0 && !$isFooter) {
            $shopMenuParams = array_merge($shopMenuParams, array(
                'attributes' => array('class' => 'dropdown'),
                'childrenAttributes' => array('class' => 'dropdown-menu'),
                'linkAttributes' => array('class' => 'dropdown-toggle', 'data-toggle' => 'dropdown', 'data-target' => '#'),
                'label' => 'Our products <b class="caret caret-menu"></b>',
                'extras' => array(
                    'safe_label' => true,
                )
            ));
        }

        if ($isFooter) {
            $shopMenuParams = array_merge($shopMenuParams, array(
                'attributes' => array('class' => 'span2'),
                "childrenAttributes" => array('class' => 'nav')
            ));
        }

        $shop = $menu->addChild('Shop', $shopMenuParams);

        foreach ($shopCategories as $categ) {
            $shop->addChild($categ->getName(), array(
                'route' => 'sonata_category_view',
                'routeParameters' => array(
                    'categoryId' => $categ->getId(),
                    'slug' => $categ->getSlug())
                )
            );
        }

        $dropdownExtrasOptions = $isFooter ? array(
            'uri' => "#",
            'attributes' => array('class' => 'span2'),
            'childrenAttributes' => array('class' => 'nav'),
        ) : array(
            'uri' => "#",
            'attributes' => array('class' => 'dropdown'),
            'childrenAttributes' => array('class' => 'dropdown-menu'),
            'linkAttributes' => array('class' => 'dropdown-toggle', 'data-toggle' => 'dropdown', 'data-target' => '#'),
            'label' => 'Our solutions <b class="caret caret-menu"></b>',
            'extras' => array(
                'safe_label' => true,
            )
        );
        $extras = $factory->createItem('Discover', $dropdownExtrasOptions);

        $extras->addChild('Gallery', array('route' => 'sonata_media_gallery_index'));
        $extras->addChild('Media & SEO', array('route' => 'sonata_demo_media'));

        $menu->addChild($extras);

        $menu->addChild('Admin', array(
            'route' => 'page_slug',
            'routeParameters' => array(
                'path' => '/user'
            )
        ));

        if ($isFooter) {
            $menu->addChild('Legal notes', array(
                'route' => 'page_slug',
                'routeParameters' => array(
                    'path' => '/legal-notes',
                )
            ));
        }

        return $menu;
    }

    public function footerMenu(FactoryInterface $factory, array $options)
    {
        return $this->mainMenu($factory, array_merge($options, array('is_footer' => true)));
    }
}