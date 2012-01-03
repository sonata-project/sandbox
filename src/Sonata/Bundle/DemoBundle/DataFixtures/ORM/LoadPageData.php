<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Bundle\DemoBunle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;

use Sonata\NewsBundle\Model\CommentInterface;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadPageData extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
{
    private $container;

    function getOrder()
    {
        return 4;
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load($manager)
    {
        $this->createGlobalPage();
        $this->createHomePage();
        $this->createBlogIndex();
        $this->createGalleryIndex();
    }

    public function createBlogIndex()
    {
        $pageManager = $this->getPageManager();

        $blogIndex = $pageManager->createNewPage();
        $blogIndex->setSlug('blog');
        $blogIndex->setUrl('/blog');
        $blogIndex->setName('News');
        $blogIndex->setEnabled(true);
        $blogIndex->setDecorate(1);
        $blogIndex->setRequestMethod('GET|POST|HEAD|DELETE|PUT');
        $blogIndex->setTemplateCode('default');
        $blogIndex->setRouteName('sonata_news_home');
        $blogIndex->setParent($this->getReference('page-homepage'));

        $pageManager->save($blogIndex);
    }

    public function createGalleryIndex()
    {
        $pageManager = $this->getPageManager();
        $blockManager = $this->getBlockManager();

        $galleryIndex = $pageManager->createNewPage();
        $galleryIndex->setSlug('gallery');
        $galleryIndex->setUrl('/media/gallery');
        $galleryIndex->setName('Gallery');
        $galleryIndex->setEnabled(true);
        $galleryIndex->setDecorate(1);
        $galleryIndex->setRequestMethod('GET|POST|HEAD|DELETE|PUT');
        $galleryIndex->setTemplateCode('default');
        $galleryIndex->setRouteName('sonata_media_gallery_index');
        $galleryIndex->setParent($this->getReference('page-homepage'));

        // CREATE A HEADER BLOCK
        $galleryIndex->addBlocks($content = $blockManager->createNewContainer(array(
            'enabled' => true,
            'page' => $galleryIndex,
            'name' => 'content_top',
        )));

        // add a block text
        $content->addChildren($text = $blockManager->create());
        $text->setType('sonata.page.block.text');
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

    public function createHomePage()
    {
        $pageManager = $this->getPageManager();
        $blockManager = $this->getBlockManager();

        $faker = $this->getFaker();

        $this->addReference('page-homepage', $homepage = $pageManager->createNewPage());
        $homepage->setSlug('/');
        $homepage->setUrl('/');
        $homepage->setName('homepage');
        $homepage->setEnabled(true);
        $homepage->setDecorate(0);
        $homepage->setRequestMethod('GET|POST|HEAD|DELETE|PUT');
        $homepage->setTemplateCode('default');
        $homepage->setRouteName('homepage');

        $pageManager->save($homepage);

        // CREATE A HEADER BLOCK
        $homepage->addBlocks($content = $blockManager->createNewContainer(array(
            'enabled' => true,
            'page' => $homepage,
            'name' => 'content',
        )));

        $blockManager->save($content);

        // add a block text
        $content->addChildren($text = $blockManager->create());
        $text->setType('sonata.page.block.text');
        $text->setSetting('content', <<<CONTENT
<h1>Welcome</h1>

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
CONTENT
);
        $text->setPosition(1);
        $text->setEnabled(true);
        $text->setPage($homepage);

        // add a gallery
        $content->addChildren($gallery = $blockManager->create());
        $gallery->setType('sonata.media.block.gallery');
        $gallery->setSetting('galleryId', $this->getReference('media-gallery')->getId());
        $gallery->setSetting('title', $faker->sentence(4));
        $gallery->setSetting('context', 'default');
        $gallery->setSetting('format', 'big');
        $gallery->setPosition(2);
        $gallery->setEnabled(true);
        $gallery->setPage($homepage);

        $content->addChildren($text = $blockManager->create());
        $text->setType('sonata.page.block.text');

        $text->setPosition(3);
        $text->setEnabled(true);
        $text->setSetting('content', <<<CONTENT
<h3>Sonata's bundles</h3>

<p>
    Some bundles does not have direct visual representation as they provide services. However, others does have
    a lot to show :

    <ul>
        <li><a href="/admin">Admin (SonataAdminBundle)</a></li>
        <li><a href="/blog">Blog (SonataNewsBundle)</a></li>
    </ul>
</p>
CONTENT
);



        $pageManager->save($homepage);
    }

    public function createGlobalPage()
    {
        $pageManager = $this->getPageManager();
        $blockManager = $this->getBlockManager();

        $faker = $this->getFaker();

        $global = $pageManager->createNewPage();
        $global->setName('global');
        $global->setRouteName('global');

        $pageManager->save($global);

        // CREATE A HEADER BLOCK
        $global->addBlocks($title = $blockManager->createNewContainer(array(
            'enabled' => true,
            'page' => $global,
            'name' => 'title',
        )));

        $title->addChildren($text = $blockManager->create());

        $text->setType('sonata.page.block.text');
        $text->setSetting('content', '<h2><a href="/">Sonata Sandbox</a></h2>');
        $text->setPosition(1);
        $text->setEnabled(true);
        $text->setPage($global);

        $global->addBlocks($header = $blockManager->createNewContainer(array(
            'enabled' => true,
            'page' => $global,
            'name' => 'header',
        )));


        $header->addChildren($menu = $blockManager->create());

        $menu->setType('sonata.page.block.children_pages');
        $menu->setSetting('current', false);
        $menu->setPosition(1);
        $menu->setEnabled(true);
        $menu->setPage($global);

        $global->addBlocks($footer = $blockManager->createNewContainer(array(
            'enabled' => true,
            'page' => $global,
            'name' => 'footer',
        )));

        $footer->addChildren($text = $blockManager->create());

        $text->setType('sonata.page.block.text');
        $text->setSetting('content', <<<FOOTER
        <a href="/admin/dashboard">Access to the backend</a> (user: admin, password: admin) <br />
        <a href="http://www.sonata-project.org">Sonata Project</a> sandbox demonstration.

<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-25614705-2']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
FOOTER
);
        $text->setPosition(1);
        $text->setEnabled(true);
        $text->setPage($global);

        $pageManager->save($global);
    }

    /**
     * @return \Sonata\PageBundle\Model\PageManagerInterface
     */
    public function getPageManager()
    {
        return $this->container->get('sonata.page.manager.page');
    }

    /**
     * @return \Sonata\PageBundle\Model\BlockManagerInterface
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
}