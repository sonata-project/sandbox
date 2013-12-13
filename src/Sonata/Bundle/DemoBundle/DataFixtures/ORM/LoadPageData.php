<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Bundle\DemoBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

use Sonata\PageBundle\Model\SiteInterface;
use Sonata\PageBundle\Model\PageInterface;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadPageData extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
{
    private $container;

    public function getOrder()
    {
        return 4;
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $site = $this->createSite();
        $this->createGlobalPage($site);
        $this->createHomePage($site);
        $this->createBlogIndex($site);
        $this->createGalleryIndex($site);
        $this->createMediaPage($site);
        $this->createProductPage($site);
        $this->createBasketPage($site);
        $this->createUserPage($site);
        $this->createLegalNotesPage($site);

        $this->createSubSite();
    }

    /**
     * @return SiteInterface $site
     */
    public function createSite()
    {
        $site = $this->getSiteManager()->create();

        $site->setHost('localhost');
        $site->setEnabled(true);
        $site->setName('localhost');
        $site->setEnabledFrom(new \DateTime('now'));
        $site->setEnabledTo(new \DateTime('+10 years'));
        $site->setRelativePath("");
        $site->setIsDefault(true);

        $this->getSiteManager()->save($site);

        return $site;
    }

    public function createSubSite()
    {
        $site = $this->getSiteManager()->create();

        $site->setHost('localhost');
        $site->setEnabled(true);
        $site->setName('sub site');
        $site->setEnabledFrom(new \DateTime('now'));
        $site->setEnabledTo(new \DateTime('+10 years'));
        $site->setRelativePath("/sub-site");
        $site->setIsDefault(false);

        $this->getSiteManager()->save($site);

        return $site;
    }

    /**
     * @param SiteInterface $site
     */
    public function createBlogIndex(SiteInterface $site)
    {
        $pageManager = $this->getPageManager();

        $blogIndex = $pageManager->create();
        $blogIndex->setSlug('blog');
        $blogIndex->setUrl('/blog');
        $blogIndex->setName('News');
        $blogIndex->setEnabled(true);
        $blogIndex->setDecorate(1);
        $blogIndex->setRequestMethod('GET|POST|HEAD|DELETE|PUT');
        $blogIndex->setTemplateCode('default');
        $blogIndex->setRouteName('sonata_news_home');
        $blogIndex->setParent($this->getReference('page-homepage'));
        $blogIndex->setSite($site);

        $pageManager->save($blogIndex);
    }

    /**
     * @param SiteInterface $site
     */
    public function createGalleryIndex(SiteInterface $site)
    {
        $pageManager = $this->getPageManager();
        $blockManager = $this->getBlockManager();
        $blockInteractor = $this->getBlockInteractor();

        $galleryIndex = $pageManager->create();
        $galleryIndex->setSlug('gallery');
        $galleryIndex->setUrl('/media/gallery');
        $galleryIndex->setName('Gallery');
        $galleryIndex->setEnabled(true);
        $galleryIndex->setDecorate(1);
        $galleryIndex->setRequestMethod('GET|POST|HEAD|DELETE|PUT');
        $galleryIndex->setTemplateCode('default');
        $galleryIndex->setRouteName('sonata_media_gallery_index');
        $galleryIndex->setParent($this->getReference('page-homepage'));
        $galleryIndex->setSite($site);

        // CREATE A HEADER BLOCK
        $galleryIndex->addBlocks($content = $blockInteractor->createNewContainer(array(
            'enabled' => true,
            'page' => $galleryIndex,
            'code' => 'content_top',
        )));

        $content->setName('The content_top container');

        // add a block text
        $content->addChildren($text = $blockManager->create());
        $text->setType('sonata.block.service.text');
        $text->setSetting('content', <<<CONTENT

<p>
    This current text is defined in a <code>text block</code> linked to a custom symfony action <code>GalleryController::indexAction</code>
    the SonataPageBundle can encapsulate an action into a dedicated template. <br /><br />

    If you are connected as an admin you can click on <code>Show Zone</code> to see the different editable areas. Once
    areas are displayed, just double click on one to edit it.
</p>

<h1>Gallery List</h1>
CONTENT
);
        $text->setPosition(1);
        $text->setEnabled(true);
        $text->setPage($galleryIndex);

        $pageManager->save($galleryIndex);
    }

    /**
     * @param SiteInterface $site
     */
    public function createHomePage(SiteInterface $site)
    {
        $pageManager = $this->getPageManager();
        $blockManager = $this->getBlockManager();
        $blockInteractor = $this->getBlockInteractor();

        $this->addReference('page-homepage', $homepage = $pageManager->create());
        $homepage->setSlug('/');
        $homepage->setUrl('/');
        $homepage->setName('homepage');
        $homepage->setEnabled(true);
        $homepage->setDecorate(0);
        $homepage->setRequestMethod('GET|POST|HEAD|DELETE|PUT');
        $homepage->setTemplateCode('2columns');
        $homepage->setRouteName(PageInterface::PAGE_ROUTE_CMS_NAME);
        $homepage->setSite($site);

        $pageManager->save($homepage);

        // CREATE A HEADER BLOCK
        $homepage->addBlocks($contentTop = $blockInteractor->createNewContainer(array(
            'enabled' => true,
            'page' => $homepage,
            'code' => 'content_top',
        )));

        $contentTop->setName('The container top container');

        $blockManager->save($contentTop);

        // add a block text
        $contentTop->addChildren($text = $blockManager->create());
        $text->setType('sonata.block.service.text');
        $text->setSetting('content', <<<CONTENT
<div class="span3"><h2>Welcome</h2></div>
<div class="span9">
    <p>
        This page is a demo of the Sonata Sandbox available on <a href="https://github.com/sonata-project/sandbox">github</a>.
        This demo try to be interactive so you will be able to found out the different features provided by the Sonata's Bundle.
    </p>

    <p>
        First this page and all the other pages are served by the <code>SonataPageBundle</code>, a page is composed by different
        blocks. A block is linked to a service. For instance the current gallery is served by a
        <a href="https://github.com/sonata-project/SonataMediaBundle/blob/master/Block/GalleryBlockService.php">Block service</a>
        provided by the <code>SonataMediaBundle</code>.
    </p>
</div>
CONTENT
);
        $text->setPosition(1);
        $text->setEnabled(true);
        $text->setPage($homepage);


        $homepage->addBlocks($content = $blockInteractor->createNewContainer(array(
            'enabled' => true,
            'page' => $homepage,
            'code' => 'content',
        )));
        $content->setName('The content container');
        $blockManager->save($content);

        // Add media gallery block
        $content->addChildren($gallery = $blockManager->create());
        $gallery->setType('sonata.media.block.gallery');
        $gallery->setSetting('galleryId', $this->getReference('media-gallery')->getId());
        $gallery->setSetting('title', 'Media gallery');
        $gallery->setSetting('context', 'default');
        $gallery->setSetting('format', 'big');
        $gallery->setPosition(1);
        $gallery->setEnabled(true);
        $gallery->setPage($homepage);

        // Add recent products block
        $content->addChildren($newProductsBlock = $blockManager->create());
        $newProductsBlock->setType('sonata.product.block.recent_products');
        $newProductsBlock->setSetting('number', 4);
        $newProductsBlock->setSetting('title', 'New products');
        $newProductsBlock->setPosition(2);
        $newProductsBlock->setEnabled(true);
        $newProductsBlock->setPage($homepage);

        $pageManager->save($homepage);
    }

    /**
     * @param SiteInterface $site
     */
    public function createProductPage(SiteInterface $site)
    {
        $pageManager = $this->getPageManager();

        $category = $pageManager->create();

        $category->setSlug('shop-category');
        $category->setUrl('/shop/category');
        $category->setName('Shop');
        $category->setEnabled(true);
        $category->setDecorate(1);
        $category->setRequestMethod('GET|POST|HEAD|DELETE|PUT');
        $category->setTemplateCode('default');
        $category->setRouteName('sonata_category_index');
        $category->setSite($site);
        $category->setParent($this->getReference('page-homepage'));

        $pageManager->save($category);
    }

    /**
     * @param SiteInterface $site
     */
    public function createBasketPage(SiteInterface $site)
    {
        $pageManager = $this->getPageManager();

        $basket = $pageManager->create();

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

        $pageManager->save($basket);
    }

    /**
     * @param SiteInterface $site
     */
    public function createMediaPage(SiteInterface $site)
    {
        $pageManager = $this->getPageManager();

        $this->addReference('page-media', $media = $pageManager->create());
        $media->setSlug('/media');
        $media->setUrl('/media');
        $media->setName('Media & Seo');
        $media->setEnabled(true);
        $media->setDecorate(1);
        $media->setRequestMethod('GET|POST|HEAD|DELETE|PUT');
        $media->setTemplateCode('default');
        $media->setRouteName('sonata_demo_media');
        $media->setSite($site);
        $media->setParent($this->getReference('page-homepage'));

        $pageManager->save($media);
    }

    /**
     * @param SiteInterface $site
     */
    public function createUserPage(SiteInterface $site)
    {
        $pageManager = $this->getPageManager();
        $blockManager = $this->getBlockManager();
        $blockInteractor = $this->getBlockInteractor();

        $this->addReference('page-user', $userPage = $pageManager->create());
        $userPage->setSlug('/user');
        $userPage->setUrl('/user');
        $userPage->setName('Admin');
        $userPage->setEnabled(true);
        $userPage->setDecorate(1);
        $userPage->setRequestMethod('GET|POST|HEAD|DELETE|PUT');
        $userPage->setTemplateCode('default');
        $userPage->setRouteName('page_slug');
        $userPage->setSite($site);
        $userPage->setParent($this->getReference('page-homepage'));

        $userPage->addBlocks($content = $blockInteractor->createNewContainer(array(
            'enabled' => true,
            'page' => $userPage,
            'code' => 'content_top',
        )));

        $content->setName('The content_top container');

        // add a block text
        $content->addChildren($text = $blockManager->create());
        $text->setType('sonata.block.service.text');
        $text->setSetting('content', <<<CONTENT

<h2>Admin Bundle</h2>

<div>
    You can connect to the <a href="/admin/dashboard">admin section</a> by using two different accounts : <br>

    <ul>
        <li>Standard user: johndoe / johndoe</li>
        <li>Admin user: admin / admin</li>
        <li>Two step verification admin user: secure / secure - Key: 4YU4QGYPB63HDN2C</li>
    </ul>

    <h3>Two Step Verification</h3>
    The <b>secure</b> account is a demo of the Two Step Verification provided by
    the <a href="http://sonata-project.org/bundles/user/2-0/doc/reference/two_step_validation.html">Sonata User Bundle</a>

    <br />
    <br />
    <center>
        <img src="/bundles/sonatademo/images/secure_qr_code.png" class="img-polaroid" />
        <br />
        <em>Take a shot of this QR Code with <a href="https://support.google.com/accounts/bin/answer.py?hl=en&answer=1066447">Google Authenticator</a></em>
    </center>

</div>

CONTENT
);
        $text->setPosition(1);
        $text->setEnabled(true);
        $text->setPage($userPage);


        $pageManager->save($userPage);
    }

    /**
     * @param SiteInterface $site
     */
    public function createLegalNotesPage(SiteInterface $site)
    {
        $pageManager = $this->getPageManager();
        $blockManager = $this->getBlockManager();
        $blockInteractor = $this->getBlockInteractor();

        $page = $pageManager->create();
        $page->setSlug('/legal-notes');
        $page->setUrl('/legal-notes');
        $page->setName('Legal notes');
        $page->setEnabled(true);
        $page->setDecorate(1);
        $page->setRequestMethod('GET|POST|HEAD|DELETE|PUT');
        $page->setTemplateCode('default');
        $page->setRouteName('page_slug');
        $page->setSite($site);
        $page->setParent($this->getReference('page-homepage'));

        $page->addBlocks($content = $blockInteractor->createNewContainer(array(
            'enabled' => true,
            'page' => $page,
            'code' => 'content_top',
        )));

        // add the content block filled with text
        $content->addChildren($text = $blockManager->create());
        $text->setType('sonata.block.service.text');
        $text->setSetting('content', <<<CONTENT

<h2>Legal notes</h2>
<p>Sonata relays on the following open source libraries.</p>
<section>
    <h3>Backend and core</h3>
    <ul>
        <li><a href="http://symfony.com" title="Symfony, PHP framework official website">Symfony</a></li>
        <li><a href="http://twig.sensiolabs.org/" title="Twig, PHP template engine">Twig</a></li>
        <li><a href="http://www.doctrine-project.org/" title="Doctrine, PHP ORM">Doctrine</a></li>
    </ul>
</section>
<section>
    <h3>Frontend</h3>
    <ul>
        <li><a href="http://jquery.com/" title="jQuery javascript library">jQuery</a></li>
        <li><a href="http://getbootstrap.com/" title="Twitter Bootstrap CSS and Javascript framework">Twitter Bootstrap</a></li>
        <li><a href="http://glyphicons.com/" title="GLYPHICONS icons">GLYPHICONS free</a></li>
    </ul>
</section>
<section>
    <h3>Testing and miscellaneous tools</h3>
    <ul>
        <li><a href="https://www.github.com" title="Github, code distribution tool">Github</a></li>
        <li><a href="http://getcomposer.org" title="Composer, dependency management tool">Composer</a></li>
        <li><a href="https://packagist.org/" title="Packagist, PHP packages repository">Packagist</a></li>
        <li><a href="https://travis-ci.org/" title="Travis CI, continuous integration tool">Travis CI</a></li>
        <li><a href="http://phpunit.de/" title="PHPUnit, PHP unit testing library">PHPUnit</a></li>
        <li><a href="http://behat.org/" title="Behat, test driven development tool">Behat</a></li>
    </ul>
</section>
CONTENT
        );
        $text->setPosition(1);
        $text->setEnabled(true);
        $text->setPage($page);

        $pageManager->save($page);
    }

    /**
     * @param SiteInterface $site
     */
    public function createGlobalPage(SiteInterface $site)
    {
        $pageManager = $this->getPageManager();
        $blockManager = $this->getBlockManager();
        $blockInteractor = $this->getBlockInteractor();

        $global = $pageManager->create();
        $global->setName('global');
        $global->setRouteName('_page_internal_global');
        $global->setSite($site);

        $pageManager->save($global);

        // CREATE A HEADER BLOCK
        $global->addBlocks($title = $blockInteractor->createNewContainer(array(
            'enabled' => true,
            'page' => $global,
            'code' => 'title',
        )));

        $title->setName('The title container');

        $title->addChildren($text = $blockManager->create());

        $text->setType('sonata.block.service.text');
        $text->setSetting('content', '<h2><a href="/">Sonata Demo</a></h2>');
        $text->setPosition(1);
        $text->setEnabled(true);
        $text->setPage($global);

        $global->addBlocks($header = $blockInteractor->createNewContainer(array(
            'enabled' => true,
            'page' => $global,
            'code' => 'header',
        )));

        $header->setName('The header container');

        $header->addChildren($account = $blockManager->create());

        $account->setType('sonata.user.block.account');
        $account->setPosition(1);
        $account->setEnabled(true);
        $account->setPage($global);

        $header->addChildren($basket = $blockManager->create());

        $basket->setType('sonata.basket.block.nb_items');
        $basket->setPosition(2);
        $basket->setEnabled(true);
        $basket->setPage($global);


        $header->addChildren($menu = $blockManager->create());

        $menu->setType('sonata.block.service.menu');
        $menu->setSetting('menu_name', "SonataDemoBundle:Builder:mainMenu");
        $menu->setSetting('safe_labels', true);
        $menu->setPosition(3);
        $menu->setEnabled(true);
        $menu->setPage($global);

        $global->addBlocks($footer = $blockInteractor->createNewContainer(array(
            'enabled' => true,
            'page'    => $global,
            'code'    => 'footer'
        ), function ($container) {
            $container->setSetting('layout', <<<FOOTER
<div class="row-fluid page-footer">{{ CONTENT }}</div>
<hr />
<div style="margin: 20px 0;">
    © <a href="http://www.sonata-project.org">Sonata Project</a> provides Sonata demo 2010 - 2013 // Open Software License ("OSL") v. 3.0<br/>
    Using <a href="http://www.glyphicons.com" target="_blank">GLYPHICONS.com</a> free icons released under <a href="http://creativecommons.org/licenses/by/3.0/" target="_blank">CC BY 3.0 license</a>
</div>
FOOTER
            );
        }));

        $footer->setName('The footer container');

        // Footer : add 3 children block containers (left, center, right)
        $footer->addChildren($footerLeft = $blockInteractor->createNewContainer(array(
            'enabled' => true,
            'page'    => $global,
            'code'    => 'content'
        ), function ($container) {
            $container->setSetting('layout', '<div class="span4">{{ CONTENT }}</div>');
        }));

        $footer->addChildren($footerCenter = $blockInteractor->createNewContainer(array(
            'enabled' => true,
            'page'    => $global,
            'code'    => 'content',
        ), function ($container) {
            $container->setSetting('layout', '<div class="span4">{{ CONTENT }}</div>');
        }));

        $footer->addChildren($footerRight = $blockInteractor->createNewContainer(array(
            'enabled' => true,
            'page'    => $global,
            'code'    => 'content'
        ), function ($container) {
            $container->setSetting('layout', '<div class="span4">{{ CONTENT }}</div>');
        }));

        // Footer left: add a simple text block
        $footerLeft->addChildren($text = $blockManager->create());

        $text->setType('sonata.block.service.text');
        $text->setSetting('content', '<h3>Sonata Demo</h3><p class="handcraft">Handcrafted in France with love ♥</p>');

        $text->setPosition(1);
        $text->setEnabled(true);
        $text->setPage($global);

        // Footer center: add menu links
        $footerCenter->addChildren($text = $blockManager->create());

        $text->setType('sonata.block.service.text');
        $text->setSetting('content', <<<CONTENT
<ul class="links">
    <li><a href="#">Who we are</a></li>
    <li><a href="#">Client testimonials</a></li>
    <li><a href="#">Press</a></li>
    <li><a href="#">FAQ</a></li>
    <li><a href="#">Contact us</a></li>
    <li><a href="#">Terms & conditions</a></li>
</ul>
CONTENT
        );

        $text->setPosition(1);
        $text->setEnabled(true);
        $text->setPage($global);

        // Footer right: add social links
        $footerRight->addChildren($text = $blockManager->create());

        $text->setType('sonata.block.service.text');
        $text->setSetting('content', <<<CONTENT
<ul class="links">
    <li><a target="_blank" href="http://www.twitter.com/sonataproject">Twitter</a></li>
    <li><a target="_blank" href="https://groups.google.com/forum/#!forum/sonata-devs">Google Group</a></li>
</ul>
CONTENT
        );

        $text->setPosition(1);
        $text->setEnabled(true);
        $text->setPage($global);

        $pageManager->save($global);
    }

    /**
     * @return \Sonata\PageBundle\Model\SiteManagerInterface
     */
    public function getSiteManager()
    {
        return $this->container->get('sonata.page.manager.site');
    }

    /**
     * @return \Sonata\PageBundle\Model\PageManagerInterface
     */
    public function getPageManager()
    {
        return $this->container->get('sonata.page.manager.page');
    }

    /**
     * @return \Sonata\BlockBundle\Model\BlockManagerInterface
     */
    public function getBlockManager()
    {
        return $this->container->get('sonata.page.manager.block');
    }

    /**
     * @return \Faker\Generator
     */
    public function getFaker()
    {
        return $this->container->get('faker.generator');
    }

    /**
     * @return \Sonata\PageBundle\Entity\BlockInteractor
     */
    public function getBlockInteractor()
    {
        return $this->container->get('sonata.page.block_interactor');
    }
}
