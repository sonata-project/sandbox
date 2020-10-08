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

namespace Sonata\Bundle\DemoBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class Builder.
 *
 * @author Hugo Briand <briand@ekino.com>
 */
class Builder implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * Creates the header menu.
     *
     * @return \Knp\Menu\ItemInterface
     */
    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $isFooter = \array_key_exists('is_footer', $options) ? $options['is_footer'] : false;

        $shopCategories = $this->container->get('sonata.classification.manager.category')->getRootCategory('product_catalog');

        $menuOptions = array_merge($options, [
            'childrenAttributes' => ['class' => 'nav nav-pills'],
        ]);

        $menu = $factory->createItem('main', $menuOptions);

        $shopMenuParams = ['route' => 'sonata_catalog_index'];

        if ($shopCategories->hasChildren() && !$isFooter) {
            $shopMenuParams = array_merge($shopMenuParams, [
                'attributes' => ['class' => 'dropdown'],
                'childrenAttributes' => ['class' => 'dropdown-menu'],
                'linkAttributes' => ['class' => 'dropdown-toggle', 'data-toggle' => 'dropdown', 'data-target' => '#'],
                'label' => 'Products <b class="caret caret-menu"></b>',
                'extras' => [
                    'safe_label' => true,
                ],
            ]);
        }

        if ($isFooter) {
            $shopMenuParams = array_merge($shopMenuParams, [
                'attributes' => ['class' => 'span2'],
                'childrenAttributes' => ['class' => 'nav'],
            ]);
        }

        $shop = $menu->addChild('Shop', $shopMenuParams);

        $menu->addChild('News', ['route' => 'sonata_news_home']);

        foreach ($shopCategories->getChildren() as $category) {
            $shop->addChild(
                $category->getName(),
                [
                'route' => 'sonata_catalog_category',
                'routeParameters' => [
                    'category_id' => $category->getId(),
                    'category_slug' => $category->getSlug(), ],
                ]
            );
        }

        $dropdownExtrasOptions = $isFooter ? [
            'uri' => '#',
            'attributes' => ['class' => 'span2'],
            'childrenAttributes' => ['class' => 'nav'],
        ] : [
            'uri' => '#',
            'attributes' => ['class' => 'dropdown'],
            'childrenAttributes' => ['class' => 'dropdown-menu'],
            'linkAttributes' => ['class' => 'dropdown-toggle', 'data-toggle' => 'dropdown', 'data-target' => '#'],
            'label' => 'Solutions <b class="caret caret-menu"></b>',
            'extras' => [
                'safe_label' => true,
            ],
        ];
        $extras = $factory->createItem('Discover', $dropdownExtrasOptions);

        $extras->addChild('Bundles', ['route' => 'page_slug', 'routeParameters' => ['path' => '/bundles']]);
        $extras->addChild('Api', ['route' => 'page_slug', 'routeParameters' => ['path' => '/api-landing']]);
        $extras->addChild('Gallery', ['route' => 'sonata_media_gallery_index']);
        $extras->addChild('Media & SEO', ['route' => 'sonata_demo_media']);

        $menu->addChild($extras);

        $menu->addChild('Admin', [
            'route' => 'page_slug',
            'routeParameters' => [
                'path' => '/user',
            ],
        ]);

        if ($isFooter) {
            $menu->addChild('Legal notes', [
                'route' => 'page_slug',
                'routeParameters' => [
                    'path' => '/legal-notes',
                ],
            ]);
        }

        return $menu;
    }

    public function footerMenu(FactoryInterface $factory, array $options)
    {
        return $this->mainMenu($factory, array_merge($options, ['is_footer' => true]));
    }
}
