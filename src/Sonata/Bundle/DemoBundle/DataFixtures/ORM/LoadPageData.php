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

namespace Sonata\Bundle\DemoBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;
use Sonata\BlockBundle\Model\BlockManagerInterface;
use Sonata\PageBundle\Model\BlockInteractorInterface;
use Sonata\PageBundle\Model\PageInterface;
use Sonata\PageBundle\Model\PageManagerInterface;
use Sonata\PageBundle\Model\SiteInterface;
use Sonata\PageBundle\Model\SiteManagerInterface;
use Twig\Environment;

class LoadPageData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @var BlockInteractorInterface
     */
    protected $blockInteractor;

    /**
     * @var BlockManagerInterface
     */
    protected $blockManager;

    /**
     * @var Generator
     */
    protected $fakeGenerator;

    /**
     * @var PageManagerInterface
     */
    protected $pageManager;

    /**
     * @var SiteManagerInterface
     */
    protected $siteManager;

    /**
     * @var Environment
     */
    protected $twig;

    /**
     * @var bool
     */
    protected $createSubsite;

    public function __construct(
        Generator $fakeGenerator,
        BlockInteractorInterface $blockInteractor,
        BlockManagerInterface $blockManager,
        SiteManagerInterface $siteManager,
        PageManagerInterface $pageManager,
        Environment $twig,
        bool $createSubsite
    ) {
        $this->blockInteractor = $blockInteractor;
        $this->blockManager = $blockManager;
        $this->fakeGenerator = $fakeGenerator;
        $this->pageManager = $pageManager;
        $this->siteManager = $siteManager;
        $this->twig = $twig;
        $this->createSubsite = $createSubsite;
    }

    public function getOrder()
    {
        return 8;
    }

    public function load(ObjectManager $manager)
    {
        $site = $this->createSite();
        $this->createGlobalPage($site);
        $this->createHomePage($site);
        $this->create404ErrorPage($site);
        $this->create500ErrorPage($site);
        $this->createBlogIndex($site);
        $this->createGalleryIndex($site);
        $this->createMediaPage($site);
        $this->createProductPage($site);
        $this->createBasketPage($site);
        $this->createTextContentPage($site, 'user', 'Admin', $this->twig->render('@SonataDemo/fixtures/site_admin.html.twig'));
        $this->createTextContentPage($site, 'api-landing', 'API', $this->twig->render('@SonataDemo/fixtures/site_api.html.twig'));
        $this->createTextContentPage($site, 'legal-notes', 'Legal notes', $this->twig->render('@SonataDemo/fixtures/site_legal_notes.html.twig'));
        $this->createTermsPage($site);

        // Create footer pages
        $this->createTextContentPage($site, 'who-we-are', 'Who we are', $this->twig->render('@SonataDemo/fixtures/site_who_we_are.html.twig'));
        $this->createTextContentPage($site, 'client-testimonials', 'Client testimonials', $this->twig->render('@SonataDemo/fixtures/site_client_testimonials.html.twig'));
        $this->createTextContentPage($site, 'press', 'Press', $this->twig->render('@SonataDemo/fixtures/site_press.html.twig'));
        $this->createTextContentPage($site, 'faq', 'FAQ', $this->twig->render('@SonataDemo/fixtures/site_faq.html.twig'));
        $this->createTextContentPage($site, 'contact-us', 'Contact us', $this->twig->render('@SonataDemo/fixtures/site_contact_us.html.twig'));
        $this->createTextContentPage($site, 'bundles', 'Sonata Bundles', $this->twig->render('@SonataDemo/fixtures/site_sonata_bundles.html.twig'));

        $this->createSubSite();
    }

    /**
     * @return SiteInterface $site
     */
    public function createSite()
    {
        $site = $this->siteManager->create();

        $site->setHost('localhost');
        $site->setEnabled(true);
        $site->setName('localhost');
        $site->setEnabledFrom(new \DateTime('now'));
        $site->setEnabledTo(new \DateTime('+10 years'));
        $site->setRelativePath('');
        $site->setIsDefault(true);

        $this->siteManager->save($site);

        return $site;
    }

    public function createSubSite()
    {
        if (true !== $this->createSubsite) {
            return;
        }

        $site = $this->siteManager->create();

        $site->setHost('localhost');
        $site->setEnabled(true);
        $site->setName('sub site');
        $site->setEnabledFrom(new \DateTime('now'));
        $site->setEnabledTo(new \DateTime('+10 years'));
        $site->setRelativePath('/sub-site');
        $site->setIsDefault(false);

        $this->siteManager->save($site);

        return $site;
    }

    public function createBlogIndex(SiteInterface $site)
    {
        $blogIndex = $this->pageManager->create();
        $blogIndex->setSlug('blog');
        $blogIndex->setUrl('/blog');
        $blogIndex->setName('News');
        $blogIndex->setTitle('News');
        $blogIndex->setEnabled(true);
        $blogIndex->setDecorate(1);
        $blogIndex->setRequestMethod('GET|POST|HEAD|DELETE|PUT');
        $blogIndex->setTemplateCode('default');
        $blogIndex->setRouteName('sonata_news_home');
        $blogIndex->setParent($this->getReference('page-homepage'));
        $blogIndex->setSite($site);

        $this->pageManager->save($blogIndex);
    }

    public function createGalleryIndex(SiteInterface $site)
    {
        $galleryIndex = $this->pageManager->create();
        $galleryIndex->setSlug('gallery');
        $galleryIndex->setUrl('/media/gallery');
        $galleryIndex->setName('Gallery');
        $galleryIndex->setTitle('Gallery');
        $galleryIndex->setEnabled(true);
        $galleryIndex->setDecorate(1);
        $galleryIndex->setRequestMethod('GET|POST|HEAD|DELETE|PUT');
        $galleryIndex->setTemplateCode('default');
        $galleryIndex->setRouteName('sonata_media_gallery_index');
        $galleryIndex->setParent($this->getReference('page-homepage'));
        $galleryIndex->setSite($site);

        // CREATE A HEADER BLOCK
        $galleryIndex->addBlocks($content = $this->blockInteractor->createNewContainer([
            'enabled' => true,
            'page' => $galleryIndex,
            'code' => 'content_top',
        ]));

        $content->setName('The content_top container');

        // add the breadcrumb
        $content->addChildren($breadcrumb = $this->blockManager->create());
        $breadcrumb->setType('sonata.page.block.breadcrumb');
        $breadcrumb->setPosition(0);
        $breadcrumb->setEnabled(true);
        $breadcrumb->setPage($galleryIndex);

        // add a block text
        $content->addChildren($text = $this->blockManager->create());
        $text->setType('sonata.block.service.text');
        $text->setSetting('content', $this->twig->render('@SonataDemo/fixtures/block_gallery.html.twig')
        );
        $text->setPosition(1);
        $text->setEnabled(true);
        $text->setPage($galleryIndex);

        $this->pageManager->save($galleryIndex);
    }

    public function createTermsPage(SiteInterface $site)
    {
        $terms = $this->pageManager->create();
        $terms->setSlug('shop-payment-terms-and-conditions');
        $terms->setUrl('/shop/payment/terms-and-conditions');
        $terms->setName('Terms and conditions');
        $terms->setTitle('Terms and conditions');
        $terms->setEnabled(true);
        $terms->setDecorate(1);
        $terms->setRequestMethod('GET|POST|HEAD|DELETE|PUT');
        $terms->setTemplateCode('default');
        $terms->setRouteName('sonata_payment_terms');
        $terms->setParent($this->getReference('page-homepage'));
        $terms->setSite($site);

        // CREATE A HEADER BLOCK
        $terms->addBlocks($content = $this->blockInteractor->createNewContainer([
            'enabled' => true,
            'page' => $terms,
            'code' => 'content_top',
        ]));
        $content->setName('The content_top container');

        // add the breadcrumb
        $content->addChildren($breadcrumb = $this->blockManager->create());
        $breadcrumb->setType('sonata.page.block.breadcrumb');
        $breadcrumb->setPosition(0);
        $breadcrumb->setEnabled(true);
        $breadcrumb->setPage($terms);

        $this->pageManager->save($terms);
    }

    public function createHomePage(SiteInterface $site)
    {
        $this->addReference('page-homepage', $homepage = $this->pageManager->create());
        $homepage->setSlug('/');
        $homepage->setUrl('/');
        $homepage->setName('Home');
        $homepage->setTitle('Homepage');
        $homepage->setEnabled(true);
        $homepage->setDecorate(0);
        $homepage->setRequestMethod('GET|POST|HEAD|DELETE|PUT');
        $homepage->setTemplateCode('2columns');
        $homepage->setRouteName(PageInterface::PAGE_ROUTE_CMS_NAME);
        $homepage->setSite($site);

        $this->pageManager->save($homepage);

        // CREATE A HEADER BLOCK
        $homepage->addBlocks($contentTop = $this->blockInteractor->createNewContainer([
            'enabled' => true,
            'page' => $homepage,
            'code' => 'content_top',
        ]));

        $contentTop->setName('The container top container');

        $this->blockManager->save($contentTop);

        // add a block text
        $contentTop->addChildren($text = $this->blockManager->create());
        $text->setType('sonata.block.service.text');
        $text->setSetting('content', $this->twig->render('@SonataDemo/fixtures/block_welcome.html.twig'));
        $text->setPosition(1);
        $text->setEnabled(true);
        $text->setPage($homepage);

        $homepage->addBlocks($content = $this->blockInteractor->createNewContainer([
            'enabled' => true,
            'page' => $homepage,
            'code' => 'content',
        ]));
        $content->setName('The content container');
        $this->blockManager->save($content);

        // Add media gallery block
        $content->addChildren($gallery = $this->blockManager->create());
        $gallery->setType('sonata.media.block.gallery');
        $gallery->setSetting('galleryId', $this->getReference('media-gallery')->getId());
        $gallery->setSetting('context', 'default');
        $gallery->setSetting('format', 'big');
        $gallery->setPosition(1);
        $gallery->setEnabled(true);
        $gallery->setPage($homepage);

        // Add recent products block
        $content->addChildren($newProductsBlock = $this->blockManager->create());
        $newProductsBlock->setType('sonata.product.block.recent_products');
        $newProductsBlock->setSetting('number', 4);
        $newProductsBlock->setSetting('title', 'New products');
        $newProductsBlock->setPosition(2);
        $newProductsBlock->setEnabled(true);
        $newProductsBlock->setPage($homepage);

        // Add homepage bottom container
        $homepage->addBlocks($bottom = $this->blockInteractor->createNewContainer([
            'enabled' => true,
            'page' => $homepage,
            'code' => 'content_bottom',
        ], static function ($container) {
            $container->setSetting('layout', '{{ CONTENT }}');
        }));
        $bottom->setName('The bottom content container');

        // Add homepage newsletter container
        $bottom->addChildren($bottomNewsletter = $this->blockInteractor->createNewContainer([
            'enabled' => true,
            'page' => $homepage,
            'code' => 'bottom_newsletter',
        ], function ($container) {
            $container->setSetting('layout', $this->twig->render('@SonataDemo/fixtures/block_newsletter_layout.html.twig'));
        }));
        $bottomNewsletter->setName('The bottom newsetter container');
        $bottomNewsletter->addChildren($newsletter = $this->blockManager->create());
        $newsletter->setType('sonata.demo.block.newsletter');
        $newsletter->setPosition(1);
        $newsletter->setEnabled(true);
        $newsletter->setPage($homepage);

        // Add homepage embed tweet container
        $bottom->addChildren($bottomEmbed = $this->blockInteractor->createNewContainer([
            'enabled' => true,
            'page' => $homepage,
            'code' => 'bottom_embed',
        ], function ($container) {
            $container->setSetting('layout', $this->twig->render('@SonataDemo/fixtures/block_twitter_layout.html.twig'));
        }));
        $bottomEmbed->setName('The bottom embedded tweet container');
        /*$bottomEmbed->addChildren($embedded = $this->blockManager->create());
        $embedded->setType('sonata.seo.block.twitter.embed');
        $embedded->setPosition(1);
        $embedded->setEnabled(true);
        $embedded->setSetting('tweet', 'https://twitter.com/dunglas/statuses/438337742565826560');
        $embedded->setSetting('lang', 'en');
        $embedded->setPage($homepage);*/
        // FixMe: does not work with latest version of guzzle (since they changed namespace)

        $this->pageManager->save($homepage);
    }

    public function createProductPage(SiteInterface $site)
    {
        $category = $this->pageManager->create();

        $category->setSlug('shop-category');
        $category->setUrl('/shop/category');
        $category->setName('Shop');
        $category->setTitle('Shop');
        $category->setEnabled(true);
        $category->setDecorate(1);
        $category->setRequestMethod('GET|POST|HEAD|DELETE|PUT');
        $category->setTemplateCode('default');
        $category->setRouteName('sonata_catalog_index');
        $category->setSite($site);
        $category->setParent($this->getReference('page-homepage'));

        $this->pageManager->save($category);
    }

    public function createBasketPage(SiteInterface $site)
    {
        $basket = $this->pageManager->create();

        $basket->setSlug('shop-basket');
        $basket->setUrl('/shop/basket');
        $basket->setName('Basket');
        $basket->setEnabled(true);
        $basket->setDecorate(1);
        $basket->setRequestMethod('GET|POST|HEAD|DELETE|PUT');
        $basket->setTemplateCode('default');
        $basket->setRouteName('sonata_basket_index');
        $basket->setSite($site);
        $basket->setParent($this->getReference('page-homepage'));

        $this->pageManager->save($basket);
    }

    public function createMediaPage(SiteInterface $site)
    {
        $this->addReference('page-media', $media = $this->pageManager->create());
        $media->setSlug('/media');
        $media->setUrl('/media');
        $media->setName('Media & Seo');
        $media->setTitle('Media & Seo');
        $media->setEnabled(true);
        $media->setDecorate(1);
        $media->setRequestMethod('GET|POST|HEAD|DELETE|PUT');
        $media->setTemplateCode('default');
        $media->setRouteName('sonata_demo_media');
        $media->setSite($site);
        $media->setParent($this->getReference('page-homepage'));

        // CREATE A HEADER BLOCK
        $media->addBlocks($content = $this->blockInteractor->createNewContainer([
            'enabled' => true,
            'page' => $media,
            'code' => 'content_top',
        ]));

        $content->setName('The content_top container');

        // add the breadcrumb
        $content->addChildren($breadcrumb = $this->blockManager->create());
        $breadcrumb->setType('sonata.page.block.breadcrumb');
        $breadcrumb->setPosition(0);
        $breadcrumb->setEnabled(true);
        $breadcrumb->setPage($media);

        $this->pageManager->save($media);
    }

    /**
     * Creates simple content pages.
     *
     * @param SiteInterface $site    A Site entity instance
     * @param string        $url     A page URL
     * @param string        $title   A page title
     * @param string        $content A text content
     */
    public function createTextContentPage(SiteInterface $site, $url, $title, $content)
    {
        $page = $this->pageManager->create();
        $page->setSlug(sprintf('/%s', $url));
        $page->setUrl(sprintf('/%s', $url));
        $page->setName($title);
        $page->setTitle($title);
        $page->setEnabled(true);
        $page->setDecorate(1);
        $page->setRequestMethod('GET|POST|HEAD|DELETE|PUT');
        $page->setTemplateCode('default');
        $page->setRouteName('page_slug');
        $page->setSite($site);
        $page->setParent($this->getReference('page-homepage'));

        $page->addBlocks($block = $this->blockInteractor->createNewContainer([
            'enabled' => true,
            'page' => $page,
            'code' => 'content_top',
        ]));

        // add the breadcrumb
        $block->addChildren($breadcrumb = $this->blockManager->create());
        $breadcrumb->setType('sonata.page.block.breadcrumb');
        $breadcrumb->setPosition(0);
        $breadcrumb->setEnabled(true);
        $breadcrumb->setPage($page);

        // Add text content block
        $block->addChildren($text = $this->blockManager->create());
        $text->setType('sonata.block.service.text');
        $text->setSetting('content', sprintf('<h2>%s</h2><div>%s</div>', $title, $content));
        $text->setPosition(1);
        $text->setEnabled(true);
        $text->setPage($page);

        $this->pageManager->save($page);
    }

    public function create404ErrorPage(SiteInterface $site)
    {
        $page = $this->pageManager->create();
        $page->setName('_page_internal_error_not_found');
        $page->setTitle('Error 404');
        $page->setEnabled(true);
        $page->setDecorate(1);
        $page->setRequestMethod('GET|POST|HEAD|DELETE|PUT');
        $page->setTemplateCode('default');
        $page->setRouteName('_page_internal_error_not_found');
        $page->setSite($site);

        $page->addBlocks($block = $this->blockInteractor->createNewContainer([
            'enabled' => true,
            'page' => $page,
            'code' => 'content_top',
        ]));

        // add the breadcrumb
        $block->addChildren($breadcrumb = $this->blockManager->create());
        $breadcrumb->setType('sonata.page.block.breadcrumb');
        $breadcrumb->setPosition(0);
        $breadcrumb->setEnabled(true);
        $breadcrumb->setPage($page);

        // Add text content block
        $block->addChildren($text = $this->blockManager->create());
        $text->setType('sonata.block.service.text');
        $text->setSetting('content', $this->twig->render('@SonataDemo/fixtures/error_404.html.twig'));
        $text->setPosition(1);
        $text->setEnabled(true);
        $text->setPage($page);

        $this->pageManager->save($page);
    }

    public function create500ErrorPage(SiteInterface $site)
    {
        $page = $this->pageManager->create();
        $page->setName('_page_internal_error_fatal');
        $page->setTitle('Error 500');
        $page->setEnabled(true);
        $page->setDecorate(1);
        $page->setRequestMethod('GET|POST|HEAD|DELETE|PUT');
        $page->setTemplateCode('default');
        $page->setRouteName('_page_internal_error_fatal');
        $page->setSite($site);

        $page->addBlocks($block = $this->blockInteractor->createNewContainer([
            'enabled' => true,
            'page' => $page,
            'code' => 'content_top',
        ]));

        // add the breadcrumb
        $block->addChildren($breadcrumb = $this->blockManager->create());
        $breadcrumb->setType('sonata.page.block.breadcrumb');
        $breadcrumb->setPosition(0);
        $breadcrumb->setEnabled(true);
        $breadcrumb->setPage($page);

        // Add text content block
        $block->addChildren($text = $this->blockManager->create());
        $text->setType('sonata.block.service.text');
        $text->setSetting('content', $this->twig->render('@SonataDemo/fixtures/error_500.html.twig'));
        $text->setPosition(1);
        $text->setEnabled(true);
        $text->setPage($page);

        $this->pageManager->save($page);
    }

    public function createGlobalPage(SiteInterface $site)
    {
        $global = $this->pageManager->create();
        $global->setName('global');
        $global->setRouteName('_page_internal_global');
        $global->setSite($site);

        $this->pageManager->save($global);

        // CREATE A HEADER BLOCK
        $global->addBlocks($header = $this->blockInteractor->createNewContainer([
            'enabled' => true,
            'page' => $global,
            'code' => 'header',
        ]));

        $header->setName('The header container');

        $header->addChildren($text = $this->blockManager->create());

        $text->setType('sonata.block.service.text');
        $text->setSetting('content', $this->twig->render('@SonataDemo/fixtures/header_demo.html.twig'));
        $text->setPosition(1);
        $text->setEnabled(true);
        $text->setPage($global);

        $global->addBlocks($headerTop = $this->blockInteractor->createNewContainer([
            'enabled' => true,
            'page' => $global,
            'code' => 'header-top',
        ], function ($container) {
            $container->setSetting('layout', $this->twig->render('@SonataDemo/fixtures/header_right_container_layout.html.twig'));
        }));

        $headerTop->setPosition(1);

        $header->addChildren($headerTop);

        $headerTop->addChildren($account = $this->blockManager->create());

        $account->setType('sonata.demo.block.account');
        $account->setPosition(1);
        $account->setEnabled(true);
        $account->setPage($global);

        $headerTop->addChildren($basket = $this->blockManager->create());

        $basket->setType('sonata.basket.block.nb_items');
        $basket->setPosition(2);
        $basket->setEnabled(true);
        $basket->setPage($global);

        $global->addBlocks($headerMenu = $this->blockInteractor->createNewContainer([
            'enabled' => true,
            'page' => $global,
            'code' => 'header-menu',
        ]));

        $headerMenu->setPosition(2);

        $header->addChildren($headerMenu);

        $headerMenu->setName('The header menu container');
        $headerMenu->setPosition(3);
        $headerMenu->addChildren($menu = $this->blockManager->create());

        $menu->setType('sonata.block.service.menu');
        $menu->setSetting('menu_name', 'SonataDemoBundle:Builder:mainMenu');
        $menu->setSetting('safe_labels', true);
        $menu->setPosition(3);
        $menu->setEnabled(true);
        $menu->setPage($global);

        $global->addBlocks($footer = $this->blockInteractor->createNewContainer([
            'enabled' => true,
            'page' => $global,
            'code' => 'footer',
        ], function ($container) {
            $container->setSetting('layout', $this->twig->render('@SonataDemo/fixtures/footer_layout.html.twig'));
        }));

        $footer->setName('The footer container');

        // Footer : add 3 children block containers (left, center, right)
        $footer->addChildren($footerLeft = $this->blockInteractor->createNewContainer([
            'enabled' => true,
            'page' => $global,
            'code' => 'content',
        ], function ($container) {
            $container->setSetting('layout', $this->twig->render('@SonataDemo/fixtures/footer_demo_layout.html.twig'));
        }));

        $footer->addChildren($footerLinksLeft = $this->blockInteractor->createNewContainer([
            'enabled' => true,
            'page' => $global,
            'code' => 'content',
        ], function ($container) {
            $container->setSetting('layout', $this->twig->render('@SonataDemo/fixtures/footer_left_layout.html.twig'));
        }));

        $footer->addChildren($footerLinksCenter = $this->blockInteractor->createNewContainer([
            'enabled' => true,
            'page' => $global,
            'code' => 'content',
        ], function ($container) {
            $container->setSetting('layout', $this->twig->render('@SonataDemo/fixtures/footer_center_layout.html.twig'));
        }));

        $footer->addChildren($footerLinksRight = $this->blockInteractor->createNewContainer([
            'enabled' => true,
            'page' => $global,
            'code' => 'content',
        ], function ($container) {
            $container->setSetting('layout', $this->twig->render('@SonataDemo/fixtures/footer_right_layout.html.twig'));
        }));

        // Footer left: add a simple text block
        $footerLeft->addChildren($text = $this->blockManager->create());

        $text->setType('sonata.block.service.text');
        $text->setSetting('content', $this->twig->render('@SonataDemo/fixtures/footer_demo_content.html.twig'));

        $text->setPosition(1);
        $text->setEnabled(true);
        $text->setPage($global);

        // Footer left links
        $footerLinksLeft->addChildren($text = $this->blockManager->create());

        $text->setType('sonata.block.service.text');
        $text->setSetting('content', $this->twig->render('@SonataDemo/fixtures/footer_product_content.html.twig'));

        $text->setPosition(1);
        $text->setEnabled(true);
        $text->setPage($global);

        // Footer middle links
        $footerLinksCenter->addChildren($text = $this->blockManager->create());

        $text->setType('sonata.block.service.text');
        $text->setSetting('content', $this->twig->render('@SonataDemo/fixtures/footer_about_content.html.twig'));

        $text->setPosition(1);
        $text->setEnabled(true);
        $text->setPage($global);

        // Footer right links
        $footerLinksRight->addChildren($text = $this->blockManager->create());

        $text->setType('sonata.block.service.text');
        $text->setSetting('content', $this->twig->render('@SonataDemo/fixtures/footer_community_content.html.twig'));

        $text->setPosition(1);
        $text->setEnabled(true);
        $text->setPage($global);

        $this->pageManager->save($global);
    }
}
