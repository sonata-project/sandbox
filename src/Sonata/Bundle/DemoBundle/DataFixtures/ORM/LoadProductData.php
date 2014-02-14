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

use Application\Sonata\MediaBundle\Entity\GalleryHasMedia;
use Application\Sonata\ProductBundle\Entity\Delivery;
use Application\Sonata\ProductBundle\Entity\Goodie;
use Application\Sonata\ProductBundle\Entity\Travel;
use Application\Sonata\ProductBundle\Entity\Package;
use Application\Sonata\ProductBundle\Entity\ProductCategory;
use Application\Sonata\ProductBundle\Entity\ProductCollection;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Sonata\ClassificationBundle\Model\CategoryInterface;
use Sonata\ClassificationBundle\Model\CollectionInterface;
use Sonata\Component\Product\ProductInterface;
use Sonata\MediaBundle\Model\GalleryInterface;
use Sonata\MediaBundle\Model\MediaInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Finder\Finder;

/**
 * Product fixtures loader.
 *
 * @author Sylvain Deloux <sylvain.deloux@ekino.com>
 */
class LoadProductData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $productPool = $this->getProductPool();

        // default media
        $defaultMedia = $this->getMediaManager()->create();
        $defaultMedia->setBinaryContent(new \SplFileInfo(__DIR__.'/../data/files/sonata_logo.png'));
        $defaultMedia->setEnabled(true);
        $defaultMedia->setName('sonata_product_default_media');
        $defaultMedia->setDescription('Default Product media');
        $this->getMediaManager()->save($defaultMedia, 'sonata_product_default', 'sonata.media.provider.image');

        /*
         * @todo
         *  - check licences for used images
         */
        $dummyCategory     = $this->getDummyCategory();
        $goodiesCategory   = $this->getGoodiesCategory();
        $travelsCategory   = $this->getTravelsCategory();
        $plushesCategory   = $this->getPlushesCategory();
        $mugCategory       = $this->getMugCategory();
        $clothesCategory   = $this->getClothesCategory();
        $shoesCategory     = $this->getShoesCategory();

        $phpCollection = $this->getPhpCollection();
        $travelCollection = $this->getTravelCollection();
        $dummyCollection = $this->getDummyCollection();

        $dummyMedia = $this->createMedia(__DIR__.'/../data/files/sonata_logo.png', 'Dummy', 'Dummy product');

        $dummyProductsCount = 501;

        for ($i = 1; $i < $dummyProductsCount; $i++) {
            // Goodies products
            $dummy = new Goodie();
            $dummy->setSku('dummy_'.$i);
            $dummy->setName(sprintf('Dummy %d', $i));
            $dummy->setSlug('dummy');
            $dummy->setDescription('<p>Dummy product. We use it to test our catalog capabilities.</p>'.$this->getLorem());
            $dummy->setRawDescription('<p>Dummy product. We use it to test our catalog capabilities.</p>'.$this->getLorem());
            $dummy->setPriceIncludingVat(true);
            $dummy->setShortDescription('<p>Dummy product. We use it to test our catalog capabilities.</p>');
            $dummy->setRawShortDescription('<p>Dummy product. We use it to test our catalog capabilities.</p>');
            $dummy->setDescriptionFormatter('richhtml');
            $dummy->setShortDescriptionFormatter('richhtml');
            $dummy->setPrice(rand(0, 2*$i));
            $dummy->setStock(rand(1, 100*$i));
            $dummy->setVatRate(20);
            $dummy->setEnabled(true);
            $manager->persist($dummy);
            $this->setReference('dummy_product_'.$i, $dummy);

            $dummy->setImage($dummyMedia);
            $this->addProductToCategory($dummy, $dummyCategory, $manager);
            $this->addProductToCollection($dummy, $dummyCollection, $manager);
            $this->addProductDeliveries($dummy, $manager);
            $this->addPackageToProduct($dummy, $manager);

            if (0 === ($i % 20)) {
                $manager->flush();
            }
        }

        // Blue PHP plush products
        $bluePhpPlush = new Goodie();
        $bluePhpPlush->setSku('php-plush-blue');
        $bluePhpPlush->setName('Blue PHP plush');
        $bluePhpPlush->setSlug('php-plush-blue');
        $bluePhpPlush->setDescription('<p>The PHP plush toy is based on the PHP world-famous elephant from Vincent Pontier (aka el roubio): Vincent evolved the logo from the three letters into an animal. More informations about Vincent <a href="http://www.elroubio.net/">http://www.elroubio.net/</a>.</p>');
        $bluePhpPlush->setRawDescription('<p>The PHP plush toy is based on the PHP world-famous elephant from Vincent Pontier (aka el roubio): Vincent evolved the logo from the three letters into an animal. More informations about Vincent <a href="http://www.elroubio.net/">http://www.elroubio.net/</a>.</p>');
        $bluePhpPlush->setPriceIncludingVat(false);
        $bluePhpPlush->setShortDescription('<p>The PHP plush toy is based on the PHP world-famous elephant from Vincent Pontier.</p>');
        $bluePhpPlush->setRawShortDescription('<p>The PHP plush toy is based on the PHP world-famous elephant from Vincent Pontier.</p>');
        $bluePhpPlush->setDescriptionFormatter('richhtml');
        $bluePhpPlush->setShortDescriptionFormatter('richhtml');
        $bluePhpPlush->setPrice(29.99);
        $bluePhpPlush->setStock(2000);
        $bluePhpPlush->setVatRate(20);
        $bluePhpPlush->setEnabled(true);
        $manager->persist($bluePhpPlush);
        $this->setReference('php_plush_blue_goodie_product', $bluePhpPlush);

        $this->addMediaToProduct(__DIR__.'/../data/files/elephpant_blue.jpg', 'Blue PHP elePHPant',
            <<<EOF
Original PHP plush based on Vincent Pontier PHP logo.
Reference - http://www.flickr.com/photos/fullo/4703273699/in/pool-35237093722@N01/
EOF
, $bluePhpPlush, 'Francesco Fullone', 'CC BY-NC-SA 2.0');
        $this->addProductToCategory($bluePhpPlush, $plushesCategory, $manager);
        $this->addProductToCategory($bluePhpPlush, $goodiesCategory, $manager);
        $this->addProductDeliveries($bluePhpPlush, $manager);
        $this->addProductToCollection($bluePhpPlush, $phpCollection, $manager);
        $this->addPackageToProduct($bluePhpPlush, $manager);

        // Green PHP plush products
        $greenPhpPlush = new Goodie();
        $greenPhpPlush->setSku('php-plush-green');
        $greenPhpPlush->setName('Green PHP plush');
        $greenPhpPlush->setSlug('php-plush-green');
        $greenPhpPlush->setDescription('<p>The PHP plush toy is based on the PHP world-famous elephant from Vincent Pontier (aka el roubio): Vincent evolved the logo from the three letters into an animal. More informations about Vincent <a href="http://www.elroubio.net/">http://www.elroubio.net/</a>.</p>');
        $greenPhpPlush->setRawDescription('<p>The PHP plush toy is based on the PHP world-famous elephant from Vincent Pontier (aka el roubio): Vincent evolved the logo from the three letters into an animal. More informations about Vincent <a href="http://www.elroubio.net/">http://www.elroubio.net/</a>.</p>');
        $greenPhpPlush->setPriceIncludingVat(false);
        $greenPhpPlush->setShortDescription('<p>The PHP plush toy is based on the PHP world-famous elephant from Vincent Pontier.</p>');
        $greenPhpPlush->setRawShortDescription('<p>The PHP plush toy is based on the PHP world-famous elephant from Vincent Pontier.</p>');
        $greenPhpPlush->setDescriptionFormatter('richhtml');
        $greenPhpPlush->setShortDescriptionFormatter('richhtml');
        $greenPhpPlush->setPrice(29.99);
        $greenPhpPlush->setStock(50);
        $greenPhpPlush->setVatRate(20);
        $greenPhpPlush->setEnabled(true);
        $manager->persist($greenPhpPlush);
        $this->setReference('php_plush_green_goodie_product', $greenPhpPlush);

        $this->addMediaToProduct(__DIR__.'/../data/files/elephpant_green.jpg', 'Green PHP elePHPant',
            <<<EOF
Green PHP plush based on Vincent Pontier PHP logo.
Reference - http://www.flickr.com/photos/ztec/9204770134/in/photostream/
EOF
            , $greenPhpPlush, 'Loïc Doubinine', 'CC BY-NC-SA 2.0');
        $this->addProductToCategory($greenPhpPlush, $plushesCategory, $manager);
        $this->addProductToCategory($greenPhpPlush, $goodiesCategory, $manager);
        $this->addProductDeliveries($greenPhpPlush, $manager);
        $this->addProductToCollection($greenPhpPlush, $phpCollection, $manager);
        $this->addPackageToProduct($greenPhpPlush, $manager);

        // Orange PHP plush products
        $orangePhpPlush = new Goodie();
        $orangePhpPlush->setSku('php-plush-orange');
        $orangePhpPlush->setName('Orange PHP plush');
        $orangePhpPlush->setSlug('php-plush-orange');
        $orangePhpPlush->setDescription('<p>The PHP plush toy is based on the PHP world-famous elephant from Vincent Pontier (aka el roubio): Vincent evolved the logo from the three letters into an animal. More informations about Vincent <a href="http://www.elroubio.net/">http://www.elroubio.net/</a>.</p>');
        $orangePhpPlush->setRawDescription('<p>The PHP plush toy is based on the PHP world-famous elephant from Vincent Pontier (aka el roubio): Vincent evolved the logo from the three letters into an animal. More informations about Vincent <a href="http://www.elroubio.net/">http://www.elroubio.net/</a>.</p>');
        $orangePhpPlush->setPriceIncludingVat(false);
        $orangePhpPlush->setShortDescription('<p>The PHP plush toy is based on the PHP world-famous elephant from Vincent Pontier.</p>');
        $orangePhpPlush->setRawShortDescription('<p>The PHP plush toy is based on the PHP world-famous elephant from Vincent Pontier.</p>');
        $orangePhpPlush->setDescriptionFormatter('richhtml');
        $orangePhpPlush->setShortDescriptionFormatter('richhtml');
        $orangePhpPlush->setPrice(29.99);
        $orangePhpPlush->setStock(50);
        $orangePhpPlush->setVatRate(20);
        $orangePhpPlush->setEnabled(true);
        $manager->persist($orangePhpPlush);
        $this->setReference('php_plush_orange_goodie_product', $orangePhpPlush);

        $this->addMediaToProduct(__DIR__.'/../data/files/elephpant_orange.jpg', 'Orange PHP elePHPant',
            <<<EOF
Orange PHP Plush based on Vincent Pontier PHP logo.
Reference - http://www.kickstarter.com/projects/eliw/php-architect-orange-elephpant
EOF
            , $orangePhpPlush, 'http://www.phparch.com/', '/');
        $this->addProductToCategory($orangePhpPlush, $plushesCategory, $manager);
        $this->addProductToCategory($orangePhpPlush, $goodiesCategory, $manager);
        $this->addProductDeliveries($orangePhpPlush, $manager);
        $this->addProductToCollection($orangePhpPlush, $phpCollection, $manager);
        $this->addPackageToProduct($orangePhpPlush, $manager);

        // PHP Mug
        $phpMug = new Goodie();
        $phpMug->setSku('PHP_MUG');
        $phpMug->setName('PHP mug');
        $phpMug->setSlug('php-mug');
        $phpMug->setDescription('<p>You love coffee and PHP ? This mug is for you.</p>'.$this->getLorem());
        $phpMug->setRawDescription('<p>You love coffee and PHP ? This mug is for you.</p>'.$this->getLorem());
        $phpMug->setPriceIncludingVat(true);
        $phpMug->setShortDescription('<p>You love coffee and PHP ? This mug is for you.</p>');
        $phpMug->setRawShortDescription('<p>You love coffee and PHP ? This mug is for you.</p>');
        $phpMug->setDescriptionFormatter('richhtml');
        $phpMug->setShortDescriptionFormatter('richhtml');

        $phpMug->setPrice(9.99);
        $phpMug->setStock(10000);
        $phpMug->setVatRate(20);
        $phpMug->setEnabled(true);
        $manager->persist($phpMug);
        $this->setReference('php_mug_goodie_product', $phpMug);

        $this->addMediaToProduct(__DIR__.'/../data/files/php_mug.jpg', 'PHP mug', 'PHP mug', $phpMug);
        $this->addProductToCategory($phpMug, $mugCategory, $manager);
        $this->addProductToCategory($phpMug, $goodiesCategory, $manager);
        $this->addProductDeliveries($phpMug, $manager);
        $this->addProductToCollection($phpMug, $phpCollection, $manager);
        $this->addPackageToProduct($phpMug, $manager);

        $phpTeeShirt = new Goodie();
        $phpTeeShirt->setSku('PHP_TSHIRT');
        $phpTeeShirt->setName('PHP tee-shirt');
        $phpTeeShirt->setSlug('php-t-shirt');
        $phpTeeShirt->setDescription('<p>A nice PHP tee-shirt, best clothe ever to pick up girls.</p>'.$this->getLorem());
        $phpTeeShirt->setRawDescription('<p>A nice PHP tee-shirt, best clothe ever to pick up girls.</p>'.$this->getLorem());

        $phpTeeShirt->setPriceIncludingVat(true);
        $phpTeeShirt->setShortDescription('<p>A nice PHP tee-shirt, best clothe ever to pick up girls.</p>');
        $phpTeeShirt->setRawShortDescription('<p>A nice PHP tee-shirt, best clothe ever to pick up girls.</p>');
        $phpTeeShirt->setDescriptionFormatter('richhtml');
        $phpTeeShirt->setShortDescriptionFormatter('richhtml');
        $phpTeeShirt->setPrice(25);
        $phpTeeShirt->setStock(0);
        $phpTeeShirt->setVatRate(20);
        $phpTeeShirt->setEnabled(true);
        $manager->persist($phpTeeShirt);
        $this->setReference('php_teeshirt_goodie_product', $phpTeeShirt);

        $this->addMediaToProduct(__DIR__.'/../data/files/php_tee_shirt.png', 'PHP tee-shirt', 'PHP tee-shirt', $phpTeeShirt);
        $this->addProductToCategory($phpTeeShirt, $clothesCategory, $manager);
        $this->addProductToCategory($phpTeeShirt, $goodiesCategory, $manager);
        $this->addProductDeliveries($phpTeeShirt, $manager);
        $this->addProductToCollection($phpTeeShirt, $phpCollection, $manager);
        $this->addPackageToProduct($phpTeeShirt, $manager);

        $maximumAir = new Goodie();
        $maximumAir->setSku('MAXIMUM_AIR_SONATA');
        $maximumAir->setName('Maximum Air Sonata Limited Edition');
        $maximumAir->setSlug('maximum-air-sonata-limited-edition');
        $maximumAir->setDescription('<p>Get this limited edition of the MAXIMUM AIR SONATA and fly over PHP bugs. True story.</p>'.$this->getLorem());
        $maximumAir->setRawDescription('<p>Get this limited edition of the MAXIMUM AIR SONATA and fly over PHP bugs. True story.</p>'.$this->getLorem());
        $maximumAir->setPriceIncludingVat(true);
        $maximumAir->setShortDescription('<p>Get this limited edition of the MAXIMUM AIR SONATA and fly over PHP bugs. True story.</p>');
        $maximumAir->setRawShortDescription('<p>Get this limited edition of the MAXIMUM AIR SONATA and fly over PHP bugs. True story.</p>');
        $maximumAir->setDescriptionFormatter('richhtml');
        $maximumAir->setShortDescriptionFormatter('richhtml');
        $maximumAir->setPrice(130);
        $maximumAir->setStock(500);
        $maximumAir->setVatRate(20);
        $maximumAir->setEnabled(false);
        $manager->persist($maximumAir);
        $this->setReference('maximum_air_sonata_product', $maximumAir);

        $this->addMediaToProduct(__DIR__.'/../data/files/sonata_logo.png', 'Maximum Air Sonata Limited edition', 'Maximum Air Sonata Limited edition', $maximumAir);
        $this->addProductToCategory($maximumAir, $goodiesCategory, $manager);
        $this->addProductToCategory($maximumAir, $shoesCategory, $manager);
        $this->addProductDeliveries($maximumAir, $manager);
        $this->addProductToCollection($maximumAir, $phpCollection, $manager);
        $this->addPackageToProduct($maximumAir, $manager);

        $maximumAirUlt = new Goodie();
        $maximumAirUlt->setSku('MAXIMUM_AIR_SONATA_ULTIMATE');
        $maximumAirUlt->setName('Maximum Air Sonata ULTIMATE Edition');
        $maximumAirUlt->setSlug('maximum-air-sonata-ultimate-edition');
        $maximumAirUlt->setDescription('<p>Get this ULTIMATE edition of the MAXIMUM AIR SONATA and fly over PHP bugs. True story. Even more powerful than the limited edition. Note that this product does not contain any media for behat testing purpose.</p>'.$this->getLorem());
        $maximumAirUlt->setRawDescription('<p>Get this ULTIMATE edition of the MAXIMUM AIR SONATA and fly over PHP bugs. True story. Even more powerful than the limited edition. Note that this product does not contain any media for behat testing purpose.</p>'.$this->getLorem());
        $maximumAirUlt->setPriceIncludingVat(true);
        $maximumAirUlt->setShortDescription('<p>Get this ULTIMATE edition of the MAXIMUM AIR SONATA and fly over PHP bugs. True story. Even more powerful than the limited edition. Note that this product does not contain any media for behat testing purpose.</p>');
        $maximumAirUlt->setRawShortDescription('<p>Get this ULTIMATE edition of the MAXIMUM AIR SONATA and fly over PHP bugs. True story. Even more powerful than the limited edition. Note that this product does not contain any media for behat testing purpose.</p>');
        $maximumAirUlt->setDescriptionFormatter('richhtml');
        $maximumAirUlt->setShortDescriptionFormatter('richhtml');
        $maximumAirUlt->setPrice(250);
        $maximumAirUlt->setStock(30);
        $maximumAirUlt->setVatRate(20);
        $maximumAirUlt->setEnabled(true);
        $manager->persist($maximumAirUlt);
        $this->setReference('maximum_air_sonata_ultimate_product', $maximumAirUlt);

        $this->addMediaToProduct(__DIR__.'/../data/files/sonata_logo.png', 'Maximum Air Sonata ULTIMATE edition', 'Maximum Air Sonata ULTIMATE edition', $maximumAirUlt);
        $this->addProductToCategory($maximumAirUlt, $goodiesCategory, $manager);
        $this->addProductToCategory($maximumAirUlt, $shoesCategory, $manager);
        $this->addProductDeliveries($maximumAirUlt, $manager);
        $this->addProductToCollection($maximumAirUlt, $phpCollection, $manager);
        $this->addPackageToProduct($maximumAirUlt, $manager);

        // Japan tour products
        $japanTravel = new Travel();
        $japanTravel->setSku('travel-japan-tour');
        $japanTravel->setName('Japan tour');
        $japanTravel->setSlug('travel-japan-tour');
        $japanTravel->setDescription(
            <<<EOF
<p>Tokyo (東京 Tōkyō?, "Eastern Capital") (Japanese: [toːkʲoː], English /ˈtoʊki.oʊ/), officially Tokyo Metropolis (東京都 Tōkyō-to?),[4] is one of the 47 prefectures of Japan.[5] Tokyo is the capital of Japan, the centre of the Greater Tokyo Area, and the largest metropolitan area in the world.[6] It is the seat of the Japanese government and the Imperial Palace, and the home of the Japanese Imperial Family. Tokyo is in the Kantō region on the southeastern side of the main island Honshu and includes the Izu Islands and Ogasawara Islands.[7] Tokyo Metropolis was formed in 1943 from the merger of the former Tokyo Prefecture (東京府 Tōkyō-fu?) and the city of Tokyo (東京市 Tōkyō-shi?).</p>

<p>Tokyo is often referred to and thought of as a city, but is officially known as a "metropolitan prefecture", which differs from a city. The Tokyo metropolitan government administers the 23 Special Wards of Tokyo (each governed as an individual city), which cover the area that was formerly the City of Tokyo before it merged and became the subsequent metropolitan prefecture in 1943. The metropolitan government also administers 39 municipalities in the western part of the prefecture and the two outlying island chains. The population of the special wards is over 9 million people, with the total population of the prefecture exceeding 13 million. The prefecture is part of the world's most populous metropolitan area with upwards of 35 million people and the world's largest urban agglomeration economy with a GDP of US$1.479 trillion at purchasing power parity, ahead of the New York metropolitan area in 2008. The city hosts 51 of the Fortune Global 500 companies, the highest number of any city.[8]</p>
Switzerland

<p>The city is considered an alpha+ world city, listed by the GaWC's 2008 inventory[9] and ranked fourth among global cities by A.T. Kearney's 2012 Global Cities Index.[10] In 2012, Tokyo was named the most expensive city for expatriates, according to the Mercer and Economist Intelligence Unit cost-of-living surveys,[11] and in 2009 named the third Most Liveable City and the World’s Most Livable Megalopolis by the magazine Monocle.[12] The Michelin Guide has awarded Tokyo by far the most Michelin stars of any city in the world.[13][14]
Tokyo hosted the Summer Olympic Games in 1964, and is scheduled to host the games again in 2020.[15]</p>

<p>References - <a href="http://en.wikipedia.org/wiki/Tokyo" target="_blank">http://en.wikipedia.org/wiki/Tokyo</a></p>
EOF

        );
        $japanTravel->setRawDescription(
            <<<EOF
<p>Tokyo (東京 Tōkyō?, "Eastern Capital") (Japanese: [toːkʲoː], English /ˈtoʊki.oʊ/), officially Tokyo Metropolis (東京都 Tōkyō-to?),[4] is one of the 47 prefectures of Japan.[5] Tokyo is the capital of Japan, the centre of the Greater Tokyo Area, and the largest metropolitan area in the world.[6] It is the seat of the Japanese government and the Imperial Palace, and the home of the Japanese Imperial Family. Tokyo is in the Kantō region on the southeastern side of the main island Honshu and includes the Izu Islands and Ogasawara Islands.[7] Tokyo Metropolis was formed in 1943 from the merger of the former Tokyo Prefecture (東京府 Tōkyō-fu?) and the city of Tokyo (東京市 Tōkyō-shi?).</p>

<p>Tokyo is often referred to and thought of as a city, but is officially known as a "metropolitan prefecture", which differs from a city. The Tokyo metropolitan government administers the 23 Special Wards of Tokyo (each governed as an individual city), which cover the area that was formerly the City of Tokyo before it merged and became the subsequent metropolitan prefecture in 1943. The metropolitan government also administers 39 municipalities in the western part of the prefecture and the two outlying island chains. The population of the special wards is over 9 million people, with the total population of the prefecture exceeding 13 million. The prefecture is part of the world's most populous metropolitan area with upwards of 35 million people and the world's largest urban agglomeration economy with a GDP of US$1.479 trillion at purchasing power parity, ahead of the New York metropolitan area in 2008. The city hosts 51 of the Fortune Global 500 companies, the highest number of any city.[8]</p>
Switzerland

<p>The city is considered an alpha+ world city, listed by the GaWC's 2008 inventory[9] and ranked fourth among global cities by A.T. Kearney's 2012 Global Cities Index.[10] In 2012, Tokyo was named the most expensive city for expatriates, according to the Mercer and Economist Intelligence Unit cost-of-living surveys,[11] and in 2009 named the third Most Liveable City and the World’s Most Livable Megalopolis by the magazine Monocle.[12] The Michelin Guide has awarded Tokyo by far the most Michelin stars of any city in the world.[13][14]
Tokyo hosted the Summer Olympic Games in 1964, and is scheduled to host the games again in 2020.[15]</p>

<p>References - <a href="http://en.wikipedia.org/wiki/Tokyo" target="_blank">http://en.wikipedia.org/wiki/Tokyo</a></p>
EOF

        );
        $japanTravel->setPriceIncludingVat(false);
        $japanTravel->setShortDescription(
            <<<EOF
<p>Tokyo (東京 Tōkyō?, "Eastern Capital") (Japanese: [toːkʲoː], English /ˈtoʊki.oʊ/), officially Tokyo Metropolis (東京都 Tōkyō-to?),[4] is one of the 47 prefectures of Japan.[5] Tokyo is the capital of Japan, the centre of the Greater Tokyo Area, and the largest metropolitan area in the world.[6] It is the seat of the Japanese government and the Imperial Palace, and the home of the Japanese Imperial Family. Tokyo is in the Kantō region on the southeastern side of the main island Honshu and includes the Izu Islands and Ogasawara Islands.[7] Tokyo Metropolis was formed in 1943 from the merger of the former Tokyo Prefecture (東京府 Tōkyō-fu?) and the city of Tokyo (東京市 Tōkyō-shi?).</p>

<p>References - <a href="http://en.wikipedia.org/wiki/Tokyo" target="_blank">http://en.wikipedia.org/wiki/Tokyo</a></p>
EOF

        );
        $japanTravel->setRawShortDescription(
            <<<EOF
<p>
Tokyo (東京 Tōkyō?, "Eastern Capital") (Japanese: [toːkʲoː], English /ˈtoʊki.oʊ/), officially Tokyo Metropolis (東京都 Tōkyō-to?),[4] is one of the 47 prefectures of Japan.[5] Tokyo is the capital of Japan, the centre of the Greater Tokyo Area, and the largest metropolitan area in the world.[6] It is the seat of the Japanese government and the Imperial Palace, and the home of the Japanese Imperial Family. Tokyo is in the Kantō region on the southeastern side of the main island Honshu and includes the Izu Islands and Ogasawara Islands.

References - <a href="http://en.wikipedia.org/wiki/Tokyo" target="_blank">http://en.wikipedia.org/wiki/Tokyo</a>
</p>
EOF

        );
        $japanTravel->setDescriptionFormatter('richhtml');
        $japanTravel->setShortDescriptionFormatter('richhtml');
        $japanTravel->setPrice(1800);
        $japanTravel->setStock(40);
        $japanTravel->setVatRate(20);
        $japanTravel->setTravelDate(new \DateTime('2015-08-10'));
        $japanTravel->setEnabled(true);

        $manager->persist($japanTravel);
        $this->setReference('travel_japan_product', $japanTravel);

        $this->addMediaToProduct(__DIR__.'/../data/files/maha-japan/L9999744.jpg', 'Japan Travel', 'Japan Travel', $japanTravel);
        $this->addJapanGallery($japanTravel);
        $this->addProductToCategory($japanTravel, $travelsCategory, $manager);
        $this->addProductToCategory($japanTravel, $this->getReference('travels_asia_category'), $manager);
        $this->addProductToCategory($japanTravel, $this->getReference('travels_japan_category'), $manager);
        $this->addProductDeliveries($japanTravel, $manager);
        $this->addProductToCollection($japanTravel, $travelCollection, $manager);
        $this->addPackageToProduct($japanTravel, $manager);

        $travelProvider = $productPool->getProvider($japanTravel);

        // Japan tour small group variation
        $japanSmallTravel = $this->generateDefaultTravelVariation($travelProvider, $japanTravel);
        $japanSmallTravel->setName('Japan tour for small group');
        $japanSmallTravel->setSku('travel-japan-tour-5');
        $japanSmallTravel->setSlug('travel-japan-tour-5');
        $japanSmallTravel->setPriceIncludingVat(false);
        $japanSmallTravel->setPrice(1800);
        $japanSmallTravel->setTravellers(5);
        $japanSmallTravel->setTravelDate(new \DateTime('2015-08-10'));
        $japanSmallTravel->setTravelDays(9);
        $japanSmallTravel->setStock(40);

        $manager->persist($japanSmallTravel);
        $this->setReference('travel_japan_small_product', $japanSmallTravel);

        // Japan tour medium group variation
        $japanMediumTravel = $this->generateDefaultTravelVariation($travelProvider, $japanTravel);
        $japanMediumTravel->setName('Japan tour for medium group');
        $japanMediumTravel->setSku('travel-japan-tour-7');
        $japanMediumTravel->setSlug('travel-japan-tour-7');
        $japanMediumTravel->setPriceIncludingVat(false);
        $japanMediumTravel->setPrice(2050);
        $japanMediumTravel->setTravellers(7);
        $japanMediumTravel->setTravelDate(new \DateTime('2015-08-10'));
        $japanMediumTravel->setTravelDays(9);
        $japanMediumTravel->setStock(40);

        $manager->persist($japanMediumTravel);
        $this->setReference('travel_japan_medium_product', $japanMediumTravel);

        // Japan tour large group variation
        $japanLargeTravel = $this->generateDefaultTravelVariation($travelProvider, $japanTravel);
        $japanLargeTravel->setName('Japan tour for large group');
        $japanLargeTravel->setSku('travel-japan-tour-9');
        $japanLargeTravel->setSlug('travel-japan-tour-9');
        $japanLargeTravel->setPriceIncludingVat(false);
        $japanLargeTravel->setPrice(2200);
        $japanLargeTravel->setTravellers(9);
        $japanLargeTravel->setTravelDate(new \DateTime('2015-08-10'));
        $japanLargeTravel->setTravelDays(9);
        $japanLargeTravel->setStock(40);

        $manager->persist($japanLargeTravel);
        $this->setReference('travel_japan_large_product', $japanLargeTravel);

        // Japan tour extra-large group variation
        $japanExtraLargeTravel = $this->generateDefaultTravelVariation($travelProvider, $japanTravel);
        $japanExtraLargeTravel->setName('Japan tour for extra-large group');
        $japanExtraLargeTravel->setSku('travel-japan-tour-12');
        $japanExtraLargeTravel->setSlug('travel-japan-tour-12');
        $japanExtraLargeTravel->setPriceIncludingVat(false);
        $japanExtraLargeTravel->setPrice(2350);
        $japanExtraLargeTravel->setTravellers(12);
        $japanExtraLargeTravel->setTravelDate(new \DateTime('2015-08-10'));
        $japanExtraLargeTravel->setTravelDays(9);
        $japanExtraLargeTravel->setStock(40);

        $manager->persist($japanExtraLargeTravel);
        $this->setReference('travel_japan_extra_large_product', $japanExtraLargeTravel);

        // Quebec tour products
        $quebecTravel = new Travel();
        $quebecTravel->setSku('travel-quebec-tour');
        $quebecTravel->setName('Quebec tour');
        $quebecTravel->setSlug('travel-quebec-tour');
        $quebecTravel->setDescription(
            <<<EOF
<p>Quebec (Listeni/kwɨˈbɛk/ or /kɨˈbɛk/; French: Québec [kebɛk] ( listen))[7] is a province in east-central Canada.[8][9] It is the only Canadian province that has a predominantly French-speaking population, and the only one to have French as its sole provincial official language.</p>

<p>Quebec is Canada's largest province by area and its second-largest administrative division; only the territory of Nunavut is larger. It is bordered to the west by the province of Ontario, James Bay and Hudson Bay, to the north by Hudson Strait and Ungava Bay, to the east by the Gulf of Saint Lawrence and the provinces of Newfoundland and Labrador and New Brunswick. It is bordered on the south by the US states of Maine, New Hampshire, Vermont, and New York. It also shares maritime borders with Nunavut, Prince Edward Island, and Nova Scotia.</p>

<p>Quebec is Canada's second most populous province, after Ontario. Most inhabitants live in urban areas near the Saint Lawrence River between Montreal and Quebec City, the capital. English-speaking communities and English-language institutions are concentrated in the west of the island of Montreal but are also significantly present in the Outaouais, Eastern Townships, and Gaspé regions. The Nord-du-Québec region, occupying the northern half of the province, is sparsely populated and inhabited primarily by Aboriginal peoples.[10]</p>

<p>Quebec independence debates have played a large role in the politics of the province. Parti Québécois governments held referendums on sovereignty in 1980 and 1995; both were voted down by voters, the latter defeated by a very narrow margin.[11] In 2006, the House of Commons of Canada passed a symbolic motion recognizing the "Québécois as a nation within a united Canada."[12][13]</p>

<p>References - <a href="http://en.wikipedia.org/wiki/Quebec" target="_blank">http://en.wikipedia.org/wiki/Quebec</a></p>
EOF

        );
        $quebecTravel->setRawDescription(
            <<<EOF
<p>Quebec (Listeni/kwɨˈbɛk/ or /kɨˈbɛk/; French: Québec [kebɛk] ( listen))[7] is a province in east-central Canada.[8][9] It is the only Canadian province that has a predominantly French-speaking population, and the only one to have French as its sole provincial official language.</p>

<p>Quebec is Canada's largest province by area and its second-largest administrative division; only the territory of Nunavut is larger. It is bordered to the west by the province of Ontario, James Bay and Hudson Bay, to the north by Hudson Strait and Ungava Bay, to the east by the Gulf of Saint Lawrence and the provinces of Newfoundland and Labrador and New Brunswick. It is bordered on the south by the US states of Maine, New Hampshire, Vermont, and New York. It also shares maritime borders with Nunavut, Prince Edward Island, and Nova Scotia.</p>

<p>Quebec is Canada's second most populous province, after Ontario. Most inhabitants live in urban areas near the Saint Lawrence River between Montreal and Quebec City, the capital. English-speaking communities and English-language institutions are concentrated in the west of the island of Montreal but are also significantly present in the Outaouais, Eastern Townships, and Gaspé regions. The Nord-du-Québec region, occupying the northern half of the province, is sparsely populated and inhabited primarily by Aboriginal peoples.[10]</p>

<p>Quebec independence debates have played a large role in the politics of the province. Parti Québécois governments held referendums on sovereignty in 1980 and 1995; both were voted down by voters, the latter defeated by a very narrow margin.[11] In 2006, the House of Commons of Canada passed a symbolic motion recognizing the "Québécois as a nation within a united Canada."[12][13]</p>

<p>References - <a href="http://en.wikipedia.org/wiki/Quebec" target="_blank">http://en.wikipedia.org/wiki/Quebec</a></p>
EOF

        );
        $quebecTravel->setPriceIncludingVat(false);
        $quebecTravel->setShortDescription(
            <<<EOF
<p>Quebec (Listeni/kwɨˈbɛk/ or /kɨˈbɛk/; French: Québec [kebɛk] ( listen))[7] is a province in east-central Canada.[8][9] It is the only Canadian province that has a predominantly French-speaking population, and the only one to have French as its sole provincial official language.</p>

<p>References - <a href="http://en.wikipedia.org/wiki/Quebec" target="_blank">http://en.wikipedia.org/wiki/Quebec</a></p>
EOF

        );
        $quebecTravel->setRawShortDescription(
            <<<EOF
<p>
Quebec (Listeni/kwɨˈbɛk/ or /kɨˈbɛk/; French: Québec [kebɛk] ( listen))[7] is a province in east-central Canada.

References - <a href="http://en.wikipedia.org/wiki/Quebec" target="_blank">http://en.wikipedia.org/wiki/Quebec</a>
</p>
EOF

        );
        $quebecTravel->setDescriptionFormatter('richhtml');
        $quebecTravel->setShortDescriptionFormatter('richhtml');
        $quebecTravel->setPrice(850);
        $quebecTravel->setStock(50);
        $quebecTravel->setVatRate(20);
        $quebecTravel->setTravelDate(new \DateTime('2015-12-20'));
        $quebecTravel->setEnabled(true);

        $manager->persist($quebecTravel);
        $this->setReference('travel_quebec_product', $quebecTravel);

        $this->addMediaToProduct(__DIR__.'/../data/files/gilles-canada/IMG_3587.jpg', 'Quebec Travel', 'Quebec Travel', $quebecTravel);
        $this->addCanadaGallery($quebecTravel);
        $this->addProductToCategory($quebecTravel, $travelsCategory, $manager);
        $this->addProductToCategory($quebecTravel, $this->getReference('travels_north_america_category'), $manager);
        $this->addProductToCategory($quebecTravel, $this->getReference('travels_canada_category'), $manager);
        $this->addProductToCategory($quebecTravel, $this->getReference('travels_quebec_category'), $manager);
        $this->addProductDeliveries($quebecTravel, $manager);
        $this->addProductToCollection($quebecTravel, $travelCollection, $manager);
        $this->addPackageToProduct($quebecTravel, $manager);

        $travelProvider = $productPool->getProvider($quebecTravel);

        // Quebec tour small group variation
        $quebecSmallTravel = $this->generateDefaultTravelVariation($travelProvider, $quebecTravel);
        $quebecSmallTravel->setName('Quebec tour for small group');
        $quebecSmallTravel->setSku('travel-quebec-tour-5');
        $quebecSmallTravel->setSlug('travel-quebec-tour-5');
        $quebecSmallTravel->setPriceIncludingVat(false);
        $quebecSmallTravel->setPrice(850);
        $quebecSmallTravel->setTravellers(5);
        $quebecSmallTravel->setTravelDate(new \DateTime('2015-12-20'));
        $quebecSmallTravel->setTravelDays(10);
        $quebecSmallTravel->setStock(50);

        $manager->persist($quebecSmallTravel);
        $this->setReference('travel_quebec_small_product', $quebecSmallTravel);

        // Quebec tour medium group variation
        $quebecMediumTravel = $this->generateDefaultTravelVariation($travelProvider, $quebecTravel);
        $quebecMediumTravel->setName('Quebec tour for medium group');
        $quebecMediumTravel->setSku('travel-quebec-tour-7');
        $quebecMediumTravel->setSlug('travel-quebec-tour-7');
        $quebecMediumTravel->setPriceIncludingVat(false);
        $quebecMediumTravel->setPrice(1050);
        $quebecMediumTravel->setTravellers(7);
        $quebecMediumTravel->setTravelDate(new \DateTime('2015-12-20'));
        $quebecMediumTravel->setTravelDays(10);
        $quebecMediumTravel->setStock(50);

        $manager->persist($quebecMediumTravel);
        $this->setReference('travel_quebec_medium_product', $quebecMediumTravel);

        // Quebec tour large group variation
        $quebecLargeTravel = $this->generateDefaultTravelVariation($travelProvider, $quebecTravel);
        $quebecLargeTravel->setName('Quebec tour for large group');
        $quebecLargeTravel->setSku('travel-quebec-tour-9');
        $quebecLargeTravel->setSlug('travel-quebec-tour-9');
        $quebecLargeTravel->setPriceIncludingVat(false);
        $quebecLargeTravel->setPrice(1200);
        $quebecLargeTravel->setTravellers(9);
        $quebecLargeTravel->setTravelDate(new \DateTime('2015-12-20'));
        $quebecLargeTravel->setTravelDays(10);
        $quebecLargeTravel->setStock(50);

        $manager->persist($quebecLargeTravel);
        $this->setReference('travel_quebec_large_product', $quebecLargeTravel);

        // Quebec tour extra-large group variation
        $quebecExtraLargeTravel = $this->generateDefaultTravelVariation($travelProvider, $quebecTravel);
        $quebecExtraLargeTravel->setName('Quebec tour for extra-large group');
        $quebecExtraLargeTravel->setSku('travel-quebec-tour-12');
        $quebecExtraLargeTravel->setSlug('travel-quebec-tour-12');
        $quebecExtraLargeTravel->setPriceIncludingVat(false);
        $quebecExtraLargeTravel->setPrice(1350);
        $quebecExtraLargeTravel->setTravellers(12);
        $quebecExtraLargeTravel->setTravelDate(new \DateTime('2015-12-20'));
        $quebecExtraLargeTravel->setTravelDays(10);
        $quebecExtraLargeTravel->setStock(50);

        $manager->persist($quebecExtraLargeTravel);
        $this->setReference('travel_quebec_extra_large_product', $quebecExtraLargeTravel);

        // Quebec tour extra-large group variation 15 days
        $quebecExtraLargeTravel = $this->generateDefaultTravelVariation($travelProvider, $quebecTravel);
        $quebecExtraLargeTravel->setName('Quebec tour for extra-large group, 2 weeks!');
        $quebecExtraLargeTravel->setPriceIncludingVat(false);
        $quebecExtraLargeTravel->setPrice(1850);
        $quebecExtraLargeTravel->setTravellers(12);
        $quebecExtraLargeTravel->setTravelDate(new \DateTime('2015-12-20'));
        $quebecExtraLargeTravel->setTravelDays(15);
        $quebecExtraLargeTravel->setStock(50);

        $manager->persist($quebecExtraLargeTravel);
        $this->setReference('travel_quebec_extra_large_product', $quebecExtraLargeTravel);

        // Paris tour products
        $parisTravel = new Travel();
        $parisTravel->setSku('travel-paris-tour');
        $parisTravel->setName('Paris tour');
        $parisTravel->setSlug('travel-paris-tour');
        $parisTravel->setDescription(
            <<<EOF
<p>Paris (English /ˈpærɪs/, Listeni/ˈpɛrɪs/; French: [paʁi] ( listen)) is the capital and most populous city of France. It is situated on the River Seine, in the north of the country, at the heart of the Île-de-France region. Within its administrative limits (the 20 arrondissements), the city had 2,234,105 inhabitants in 2009 while its metropolitan area is one of the largest population centres in Europe with more than 12 million inhabitants.</p>

<p>An important settlement for more than two millennia, by the late 12th century Paris had become a walled cathedral city that was one of Europe's foremost centres of learning and the arts and the largest city in the Western world until the turn of the 18th century. Paris was the focal point for many important political events throughout its history, including the French Revolution. Today it is one of the world's leading business and cultural centres, and its influence in politics, education, entertainment, media, science, fashion and the arts all contribute to its status as one of the world's major cities. The city has one of the largest GDPs in the world, €607 billion (US$845 billion) as of 2011, and as a result of its high concentration of national and international political, cultural and scientific institutions is one of the world's leading tourist destinations. The Paris Region hosts the world headquarters of 30 of the Fortune Global 500 companies[6] in several business districts, notably La Défense, the largest dedicated business district in Europe.[7]</p>

<p>Centuries of cultural and political development have brought Paris a variety of museums, theatres, monuments and architectural styles. Many of its masterpieces such as the Louvre and the Arc de Triomphe are iconic buildings, especially its internationally recognized symbol, the Eiffel Tower. Long regarded as an international centre for the arts, works by history's most famous painters can be found in the Louvre, the Musée d'Orsay and its many other museums and galleries. Paris is a global hub of fashion and has been referred to as the "international capital of style", noted for its haute couture tailoring, its high-end boutiques, and the twice-yearly Paris Fashion Week. It is world renowned for its haute cuisine, attracting many of the world's leading chefs. Many of France's most prestigious universities and Grandes Écoles are in Paris or its suburbs, and France's major newspapers Le Monde, Le Figaro, Libération are based in the city, and Le Parisien in Saint-Ouen near Paris.</p>

<p>References - <a href="http://en.wikipedia.org/wiki/Paris" target="_blank">http://en.wikipedia.org/wiki/Paris</a></p>
EOF

        );
        $parisTravel->setRawDescription(
            <<<EOF
<p>Paris (English /ˈpærɪs/, Listeni/ˈpɛrɪs/; French: [paʁi] ( listen)) is the capital and most populous city of France. It is situated on the River Seine, in the north of the country, at the heart of the Île-de-France region. Within its administrative limits (the 20 arrondissements), the city had 2,234,105 inhabitants in 2009 while its metropolitan area is one of the largest population centres in Europe with more than 12 million inhabitants.</p>

<p>An important settlement for more than two millennia, by the late 12th century Paris had become a walled cathedral city that was one of Europe's foremost centres of learning and the arts and the largest city in the Western world until the turn of the 18th century. Paris was the focal point for many important political events throughout its history, including the French Revolution. Today it is one of the world's leading business and cultural centres, and its influence in politics, education, entertainment, media, science, fashion and the arts all contribute to its status as one of the world's major cities. The city has one of the largest GDPs in the world, €607 billion (US$845 billion) as of 2011, and as a result of its high concentration of national and international political, cultural and scientific institutions is one of the world's leading tourist destinations. The Paris Region hosts the world headquarters of 30 of the Fortune Global 500 companies[6] in several business districts, notably La Défense, the largest dedicated business district in Europe.[7]</p>

<p>Centuries of cultural and political development have brought Paris a variety of museums, theatres, monuments and architectural styles. Many of its masterpieces such as the Louvre and the Arc de Triomphe are iconic buildings, especially its internationally recognized symbol, the Eiffel Tower. Long regarded as an international centre for the arts, works by history's most famous painters can be found in the Louvre, the Musée d'Orsay and its many other museums and galleries. Paris is a global hub of fashion and has been referred to as the "international capital of style", noted for its haute couture tailoring, its high-end boutiques, and the twice-yearly Paris Fashion Week. It is world renowned for its haute cuisine, attracting many of the world's leading chefs. Many of France's most prestigious universities and Grandes Écoles are in Paris or its suburbs, and France's major newspapers Le Monde, Le Figaro, Libération are based in the city, and Le Parisien in Saint-Ouen near Paris.</p>

<p>References - <a href="http://en.wikipedia.org/wiki/Paris" target="_blank">http://en.wikipedia.org/wiki/Paris</a></p>
EOF

        );
        $parisTravel->setPriceIncludingVat(false);
        $parisTravel->setShortDescription(
            <<<EOF
<p>Paris (English /ˈpærɪs/, Listeni/ˈpɛrɪs/; French: [paʁi] ( listen)) is the capital and most populous city of France. It is situated on the River Seine, in the north of the country, at the heart of the Île-de-France region. Within its administrative limits (the 20 arrondissements), the city had 2,234,105 inhabitants in 2009 while its metropolitan area is one of the largest population centres in Europe with more than 12 million inhabitants.</p>

<p>References - <a href="http://en.wikipedia.org/wiki/Paris" target="_blank">http://en.wikipedia.org/wiki/Paris</a></p>
EOF

        );
        $parisTravel->setRawShortDescription(
            <<<EOF
<p>
Paris (English /ˈpærɪs/, Listeni/ˈpɛrɪs/; French: [paʁi] ( listen)) is the capital and most populous city of France. It is situated on the River Seine, in the north of the country, at the heart of the Île-de-France region.

References - <a href="http://en.wikipedia.org/wiki/Paris" target="_blank">http://en.wikipedia.org/wiki/Paris</a>
</p>
EOF

        );
        $parisTravel->setDescriptionFormatter('richhtml');
        $parisTravel->setShortDescriptionFormatter('richhtml');
        $parisTravel->setPrice(400);
        $parisTravel->setStock(85);
        $parisTravel->setVatRate(20);
        $parisTravel->setTravelDate(new \DateTime('2015-06-02'));
        $parisTravel->setEnabled(true);

        $manager->persist($parisTravel);
        $this->setReference('travel_paris_product', $parisTravel);

        $this->addMediaToProduct(__DIR__.'/../data/files/hugo-paris/IMG_3008.jpg', 'Paris Travel', 'Paris Travel', $parisTravel);
        $this->addParisGallery($parisTravel);
        $this->addProductToCategory($parisTravel, $travelsCategory, $manager);
        $this->addProductToCategory($parisTravel, $this->getReference('travels_europe_category'), $manager);
        $this->addProductToCategory($parisTravel, $this->getReference('travels_france_category'), $manager);
        $this->addProductToCategory($parisTravel, $this->getReference('travels_paris_category'), $manager);
        $this->addProductDeliveries($parisTravel, $manager);
        $this->addProductToCollection($parisTravel, $travelCollection, $manager);
        $this->addPackageToProduct($parisTravel, $manager);

        $travelProvider = $productPool->getProvider($parisTravel);

        // Paris tour small group variation
        $parisSmallTravel = $this->generateDefaultTravelVariation($travelProvider, $parisTravel);
        $parisSmallTravel->setName('Paris tour for small group');
        $parisSmallTravel->setSku('travel-paris-tour-5');
        $parisSmallTravel->setSlug('travel-paris-tour-5');
        $parisSmallTravel->setPriceIncludingVat(false);
        $parisSmallTravel->setPrice(400);
        $parisSmallTravel->setTravellers(5);
        $parisSmallTravel->setTravelDate(new \DateTime('2015-06-02'));
        $parisSmallTravel->setTravelDays(7);
        $parisSmallTravel->setStock(85);

        $manager->persist($parisSmallTravel);
        $this->setReference('travel_paris_small_product', $parisSmallTravel);

        // Paris tour medium group variation
        $parisMediumTravel = $this->generateDefaultTravelVariation($travelProvider, $parisTravel);
        $parisMediumTravel->setName('Paris tour for medium group');
        $parisMediumTravel->setSku('travel-paris-tour-7');
        $parisMediumTravel->setSlug('travel-paris-tour-7');
        $parisMediumTravel->setPriceIncludingVat(false);
        $parisMediumTravel->setPrice(700);
        $parisMediumTravel->setTravellers(7);
        $parisMediumTravel->setTravelDate(new \DateTime('2015-06-02'));
        $parisMediumTravel->setTravelDays(7);
        $parisMediumTravel->setStock(85);

        $manager->persist($parisMediumTravel);
        $this->setReference('travel_paris_medium_product', $parisMediumTravel);

        // Paris tour large group variation
        $parisLargeTravel = $this->generateDefaultTravelVariation($travelProvider, $parisTravel);
        $parisLargeTravel->setName('Paris tour for large group');
        $parisLargeTravel->setSku('travel-paris-tour-9');
        $parisLargeTravel->setSlug('travel-paris-tour-9');
        $parisLargeTravel->setPriceIncludingVat(false);
        $parisLargeTravel->setPrice(850);
        $parisLargeTravel->setTravellers(9);
        $parisLargeTravel->setTravelDate(new \DateTime('2015-06-02'));
        $parisLargeTravel->setTravelDays(7);
        $parisLargeTravel->setStock(85);

        $manager->persist($parisLargeTravel);
        $this->setReference('travel_paris_large_product', $parisLargeTravel);

        // Paris tour extra-large group variation
        $parisExtraLargeTravel = $this->generateDefaultTravelVariation($travelProvider, $parisTravel);
        $parisExtraLargeTravel->setName('Paris tour for extra-large group');
        $parisExtraLargeTravel->setSku('travel-paris-tour-12');
        $parisExtraLargeTravel->setSlug('travel-paris-tour-12');
        $parisExtraLargeTravel->setPriceIncludingVat(false);
        $parisExtraLargeTravel->setPrice(975);
        $parisExtraLargeTravel->setTravellers(12);
        $parisExtraLargeTravel->setTravelDate(new \DateTime('2015-06-02'));
        $parisExtraLargeTravel->setTravelDays(7);
        $parisExtraLargeTravel->setStock(85);

        $manager->persist($parisExtraLargeTravel);
        $this->setReference('travel_paris_extra_large_product', $parisExtraLargeTravel);

        // London tour products
        /*$londonTravel = new Travel();
        $londonTravel->setSku('travel-london-tour');
        $londonTravel->setName('London tour');
        $londonTravel->setSlug('travel-london-tour');
        $londonTravel->setDescription(
            <<<EOF
London Listeni/ˈlʌndən/ is the capital city of England and of the United Kingdom. It is the most populous region, urban zone and metropolitan area in the United Kingdom. Standing on the River Thames, London has been a major settlement for two millennia, its history going back to its founding by the Romans, who named it Londinium.[5] London's ancient core, the City of London, largely retains its 1.12-square-mile (2.9 km2) mediaeval boundaries and in 2011 had a resident population of 7,375, making it the smallest city in England. Since at least the 19th century, the term London has also referred to the metropolis developed around this core.[6] The bulk of this conurbation forms the London region[7] and the Greater London administrative area,[8][note 1] governed by the Mayor of London and the London Assembly.[9]

London is a leading global city,[10][11] with strengths in the arts, commerce, education, entertainment, fashion, finance, healthcare, media, professional services, research and development, tourism and transport all contributing to its prominence.[12] It is one of the world's leading financial centres[13][14][15] and has the fifth- or sixth-largest metropolitan area GDP in the world depending on measurement.[note 2][16][17] London is a world cultural capital.[18][19][20][21] It is the world's most-visited city as measured by international arrivals[22] and has the world's largest city airport system measured by passenger traffic.[23] London's 43 universities form the largest concentration of higher education in Europe.[24] In 2012, London became the first city to host the modern Summer Olympic Games three times.[25]

London has a diverse range of peoples and cultures, and more than 300 languages are spoken within its boundaries.[26] London had an official population of 8,308,369 in 2012,[2] making it the most populous municipality in the European Union,[27] and accounting for 12.5% of the UK population.[28] The Greater London Urban Area is the second-largest in the EU with a population of 9,787,426 according to the 2011 census.[3] The London metropolitan area is the largest in the EU with a total population of 13,614,409,[note 3][4][29] while the Greater London Authority puts the population of London metropolitan region at 21 million.[30] London had the largest population of any city in the world from around 1831 to 1925.[31]

London contains four World Heritage Sites: the Tower of London; Kew Gardens; the site comprising the Palace of Westminster, Westminster Abbey, and St Margaret's Church; and the historic settlement of Greenwich (in which the Royal Observatory, Greenwich marks the Prime Meridian, 0° longitude, and GMT).[32] Other famous landmarks include Buckingham Palace, the London Eye, Piccadilly Circus, St Paul's Cathedral, Tower Bridge, Trafalgar Square, and The Shard. London is home to numerous museums, galleries, libraries, sporting events and other cultural institutions, including the British Museum, National Gallery, Tate Modern, British Library and 40 West End theatres.[33] The London Underground is the oldest underground railway network in the world.[34][35]

References - http://en.wikipedia.org/wiki/London
EOF

        );
        $londonTravel->setRawDescription(
            <<<EOF
London Listeni/ˈlʌndən/ is the capital city of England and of the United Kingdom. It is the most populous region, urban zone and metropolitan area in the United Kingdom. Standing on the River Thames, London has been a major settlement for two millennia, its history going back to its founding by the Romans, who named it Londinium.[5] London's ancient core, the City of London, largely retains its 1.12-square-mile (2.9 km2) mediaeval boundaries and in 2011 had a resident population of 7,375, making it the smallest city in England. Since at least the 19th century, the term London has also referred to the metropolis developed around this core.[6] The bulk of this conurbation forms the London region[7] and the Greater London administrative area,[8][note 1] governed by the Mayor of London and the London Assembly.[9]

London is a leading global city,[10][11] with strengths in the arts, commerce, education, entertainment, fashion, finance, healthcare, media, professional services, research and development, tourism and transport all contributing to its prominence.[12] It is one of the world's leading financial centres[13][14][15] and has the fifth- or sixth-largest metropolitan area GDP in the world depending on measurement.[note 2][16][17] London is a world cultural capital.[18][19][20][21] It is the world's most-visited city as measured by international arrivals[22] and has the world's largest city airport system measured by passenger traffic.[23] London's 43 universities form the largest concentration of higher education in Europe.[24] In 2012, London became the first city to host the modern Summer Olympic Games three times.[25]

London has a diverse range of peoples and cultures, and more than 300 languages are spoken within its boundaries.[26] London had an official population of 8,308,369 in 2012,[2] making it the most populous municipality in the European Union,[27] and accounting for 12.5% of the UK population.[28] The Greater London Urban Area is the second-largest in the EU with a population of 9,787,426 according to the 2011 census.[3] The London metropolitan area is the largest in the EU with a total population of 13,614,409,[note 3][4][29] while the Greater London Authority puts the population of London metropolitan region at 21 million.[30] London had the largest population of any city in the world from around 1831 to 1925.[31]

London contains four World Heritage Sites: the Tower of London; Kew Gardens; the site comprising the Palace of Westminster, Westminster Abbey, and St Margaret's Church; and the historic settlement of Greenwich (in which the Royal Observatory, Greenwich marks the Prime Meridian, 0° longitude, and GMT).[32] Other famous landmarks include Buckingham Palace, the London Eye, Piccadilly Circus, St Paul's Cathedral, Tower Bridge, Trafalgar Square, and The Shard. London is home to numerous museums, galleries, libraries, sporting events and other cultural institutions, including the British Museum, National Gallery, Tate Modern, British Library and 40 West End theatres.[33] The London Underground is the oldest underground railway network in the world.[34][35]

References - http://en.wikipedia.org/wiki/London
EOF

        );
        $londonTravel->setPriceIncludingVat(false);
        $londonTravel->setShortDescription(
            <<<EOF
London Listeni/ˈlʌndən/ is the capital city of England and of the United Kingdom. It is the most populous region, urban zone and metropolitan area in the United Kingdom. Standing on the River Thames, London has been a major settlement for two millennia, its history going back to its founding by the Romans, who named it Londinium.[5] London's ancient core, the City of London, largely retains its 1.12-square-mile (2.9 km2) mediaeval boundaries and in 2011 had a resident population of 7,375, making it the smallest city in England. Since at least the 19th century, the term London has also referred to the metropolis developed around this core.[6] The bulk of this conurbation forms the London region[7] and the Greater London administrative area,[8][note 1] governed by the Mayor of London and the London Assembly.[9]

References - http://en.wikipedia.org/wiki/London
EOF

        );
        $londonTravel->setRawShortDescription(
            <<<EOF
<p>
London Listeni/ˈlʌndən/ is the capital city of England and of the United Kingdom. It is the most populous region, urban zone and metropolitan area in the United Kingdom. Standing on the River Thames, London has been a major settlement for two millennia, its history going back to its founding by the Romans, who named it Londinium.[5] London's ancient core, the City of London, largely retains its 1.12-square-mile (2.9 km2) mediaeval boundaries and in 2011 had a resident population of 7,375, making it the smallest city in England. Since at least the 19th century, the term London has also referred to the metropolis developed around this core.

References - <a href="http://en.wikipedia.org/wiki/London" target="_blank">http://en.wikipedia.org/wiki/London</a>
</p>
EOF

        );
        $londonTravel->setDescriptionFormatter('richhtml');
        $londonTravel->setShortDescriptionFormatter('richhtml');
        $londonTravel->setPrice(400);
        $londonTravel->setStock(85);
        $londonTravel->setVatRate(20);
        $londonTravel->setTravelDate(new \DateTime('2014-10-28'));
        $londonTravel->setEnabled(true);

        $manager->persist($londonTravel);
        $this->setReference('travel_london_product', $londonTravel);

        $this->addMediaToProduct(__DIR__.'/../data/files/elephpant.png', 'London Travel', 'London Travel', $londonTravel);
        $this->addProductToCategory($londonTravel, $travelsCategory, $manager);
        $this->addProductToCategory($londonTravel, $this->getReference('travels_europe_category'), $manager);
        $this->addProductToCategory($londonTravel, $this->getReference('travels_great_britain_category'), $manager);
        $this->addProductToCategory($londonTravel, $this->getReference('travels_london_category'), $manager);
        $this->addProductDeliveries($londonTravel, $manager);
        $this->addProductToCollection($londonTravel, $travelCollection, $manager);
        $this->addPackageToProduct($londonTravel, $manager);

        $travelProvider = $productPool->getProvider($londonTravel);*/

        // London tour small group variation
        /*$londonSmallTravel = $this->generateDefaultTravelVariation($travelProvider, $londonTravel);
        $londonSmallTravel->setName('London tour for small group');
        $londonSmallTravel->setSku('travel-london-tour-5');
        $londonSmallTravel->setSlug('travel-london-tour-5');
        $londonSmallTravel->setPriceIncludingVat(false);
        $londonSmallTravel->setPrice(400);
        $londonSmallTravel->setTravellers(5);
        $londonSmallTravel->setTravelDate(new \DateTime('2014-10-28'));
        $londonSmallTravel->setTravelDays(7);
        $londonSmallTravel->setStock(85);

        $manager->persist($londonSmallTravel);
        $this->setReference('travel_london_small_product', $londonSmallTravel);*/

        // London tour medium group variation
        /*$londonMediumTravel = $this->generateDefaultTravelVariation($travelProvider, $londonTravel);
        $londonMediumTravel->setName('London tour for medium group');
        $londonMediumTravel->setSku('travel-london-tour-7');
        $londonMediumTravel->setSlug('travel-london-tour-7');
        $londonMediumTravel->setPriceIncludingVat(false);
        $londonMediumTravel->setPrice(700);
        $londonMediumTravel->setTravellers(7);
        $londonMediumTravel->setTravelDate(new \DateTime('2014-10-28'));
        $londonMediumTravel->setTravelDays(7);
        $londonMediumTravel->setStock(85);

        $manager->persist($londonMediumTravel);
        $this->setReference('travel_london_medium_product', $londonMediumTravel);*/

        // London tour large group variation
        /*$londonLargeTravel = $this->generateDefaultTravelVariation($travelProvider, $londonTravel);
        $londonLargeTravel->setName('London tour for large group');
        $londonLargeTravel->setSku('travel-london-tour-9');
        $londonLargeTravel->setSlug('travel-london-tour-9');
        $londonLargeTravel->setPriceIncludingVat(false);
        $londonLargeTravel->setPrice(850);
        $londonLargeTravel->setTravellers(9);
        $londonLargeTravel->setTravelDate(new \DateTime('2014-10-28'));
        $londonLargeTravel->setTravelDays(7);
        $londonLargeTravel->setStock(85);

        $manager->persist($londonLargeTravel);
        $this->setReference('travel_london_large_product', $londonLargeTravel);*/

        // London tour extra-large group variation
        /*$londonExtraLargeTravel = $this->generateDefaultTravelVariation($travelProvider, $londonTravel);
        $londonExtraLargeTravel->setName('London tour for extra-large group');
        $londonExtraLargeTravel->setSku('travel-london-tour-12');
        $londonExtraLargeTravel->setSlug('travel-london-tour-12');
        $londonExtraLargeTravel->setPriceIncludingVat(false);
        $londonExtraLargeTravel->setPrice(975);
        $londonExtraLargeTravel->setTravellers(12);
        $londonExtraLargeTravel->setTravelDate(new \DateTime('2014-10-28'));
        $londonExtraLargeTravel->setTravelDays(7);
        $londonExtraLargeTravel->setStock(85);

        $manager->persist($londonExtraLargeTravel);
        $this->setReference('travel_london_extra_large_product', $londonExtraLargeTravel);*/

        // Switzerland tour products
        $switzerlandTravel = new Travel();
        $switzerlandTravel->setSku('travel-switzerland-tour');
        $switzerlandTravel->setName('Switzerland tour');
        $switzerlandTravel->setSlug('travel-switzerland-tour');
        $switzerlandTravel->setDescription(
            <<<EOF
<p>Switzerland (German: Schweiz[note 3] [ˈʃvaɪts]; French: Suisse [sɥis]; Italian: Svizzera [ˈzvittsera]; Romansh: Svizra [ˈʒviːtsrɐ] or [ˈʒviːtsʁːɐ]), officially the Swiss Confederation (Latin: Confoederatio Helvetica, hence its abbreviation CH), is a federal parliamentary republic consisting of 26 cantons, with Bern as the seat of the federal authorities. The country is situated in Western and Central Europe,[note 4] where it is bordered by Germany to the north, France to the west, Italy to the south, and Austria and Liechtenstein to the east. Switzerland is a landlocked country geographically divided between the Alps, the Swiss Plateau and the Jura, spanning an area of 41,285 km2 (15,940 sq mi). While the Alps occupy the greater part of the territory, the Swiss population of approximately 8 million people is concentrated mostly on the Plateau, where the largest cities are to be found. Among them are the two global cities and economic centres of Zürich and Geneva.

<p>The establishment of the Swiss Confederation is traditionally dated to 1 August 1291, which is celebrated annually as Swiss National Day. It has a long history of armed neutrality—it has not been in a state of war internationally since 1815—and did not join the United Nations until 2002. It pursues, however, an active foreign policy and is frequently involved in peace-building processes around the world.[8] Switzerland is also the birthplace of the Red Cross and home to a large number of international organizations, including the second largest UN office. On the European level, it is a founding member of the European Free Trade Association and is part of the Schengen Area – although it is notably not a member of the European Union, nor the European Economic Area. Switzerland comprises four main linguistic and cultural regions: German, French, Italian and the Romansh-speaking valleys. Therefore the Swiss, although predominantly German-speaking, do not form a nation in the sense of a common ethnic or linguistic identity; rather, the strong sense of identity and community is founded on a common historical background, shared values such as federalism and direct democracy,[9] and Alpine symbolism.[10]

<p>Switzerland has the highest wealth per adult (financial and non-financial assets) in the world according to Credit Suisse and eighth-highest per capita gross domestic product on the IMF list.[11][12] Swiss citizens have the second-highest life expectancy in the world on UN and WHO lists. Switzerland has the top rank in Bribe Payers Index indicating very low levels of business corruption. Moreover for the last five years the country has enjoyed highest economic and tourist competitiveness according to Global Competitiveness Report and Travel and Tourism Competitiveness Report respectively, both developed by the World Economic Forum. Zürich and Geneva have been ranked among cities with highest quality of life in the world with the former coming second globally according to Mercer.[13]

<p>References - <a href="http://en.wikipedia.org/wiki/Switzerland" target="_blank">http://en.wikipedia.org/wiki/Switzerland</a></p>
EOF

        );
        $switzerlandTravel->setRawDescription(
            <<<EOF
<p>Switzerland (German: Schweiz[note 3] [ˈʃvaɪts]; French: Suisse [sɥis]; Italian: Svizzera [ˈzvittsera]; Romansh: Svizra [ˈʒviːtsrɐ] or [ˈʒviːtsʁːɐ]), officially the Swiss Confederation (Latin: Confoederatio Helvetica, hence its abbreviation CH), is a federal parliamentary republic consisting of 26 cantons, with Bern as the seat of the federal authorities. The country is situated in Western and Central Europe,[note 4] where it is bordered by Germany to the north, France to the west, Italy to the south, and Austria and Liechtenstein to the east. Switzerland is a landlocked country geographically divided between the Alps, the Swiss Plateau and the Jura, spanning an area of 41,285 km2 (15,940 sq mi). While the Alps occupy the greater part of the territory, the Swiss population of approximately 8 million people is concentrated mostly on the Plateau, where the largest cities are to be found. Among them are the two global cities and economic centres of Zürich and Geneva.

<p>The establishment of the Swiss Confederation is traditionally dated to 1 August 1291, which is celebrated annually as Swiss National Day. It has a long history of armed neutrality—it has not been in a state of war internationally since 1815—and did not join the United Nations until 2002. It pursues, however, an active foreign policy and is frequently involved in peace-building processes around the world.[8] Switzerland is also the birthplace of the Red Cross and home to a large number of international organizations, including the second largest UN office. On the European level, it is a founding member of the European Free Trade Association and is part of the Schengen Area – although it is notably not a member of the European Union, nor the European Economic Area. Switzerland comprises four main linguistic and cultural regions: German, French, Italian and the Romansh-speaking valleys. Therefore the Swiss, although predominantly German-speaking, do not form a nation in the sense of a common ethnic or linguistic identity; rather, the strong sense of identity and community is founded on a common historical background, shared values such as federalism and direct democracy,[9] and Alpine symbolism.[10]

<p>Switzerland has the highest wealth per adult (financial and non-financial assets) in the world according to Credit Suisse and eighth-highest per capita gross domestic product on the IMF list.[11][12] Swiss citizens have the second-highest life expectancy in the world on UN and WHO lists. Switzerland has the top rank in Bribe Payers Index indicating very low levels of business corruption. Moreover for the last five years the country has enjoyed highest economic and tourist competitiveness according to Global Competitiveness Report and Travel and Tourism Competitiveness Report respectively, both developed by the World Economic Forum. Zürich and Geneva have been ranked among cities with highest quality of life in the world with the former coming second globally according to Mercer.[13]

<p>References - <a href="http://en.wikipedia.org/wiki/Switzerland" target="_blank">http://en.wikipedia.org/wiki/Switzerland</a></p>
EOF

        );
        $switzerlandTravel->setPriceIncludingVat(false);
        $switzerlandTravel->setShortDescription(
            <<<EOF
<p>
Switzerland (German: Schweiz[note 3] [ˈʃvaɪts]; French: Suisse [sɥis]; Italian: Svizzera [ˈzvittsera]; Romansh: Svizra [ˈʒviːtsrɐ] or [ˈʒviːtsʁːɐ]), officially the Swiss Confederation (Latin: Confoederatio Helvetica, hence its abbreviation CH), is a federal parliamentary republic consisting of 26 cantons, with Bern as the seat of the federal authorities. The country is situated in Western and Central Europe,[note 4] where it is bordered by Germany to the north, France to the west, Italy to the south, and Austria and Liechtenstein to the east. Switzerland is a landlocked country geographically divided between the Alps, the Swiss Plateau and the Jura, spanning an area of 41,285 km2 (15,940 sq mi).

References - <a href="http://en.wikipedia.org/wiki/Switzerland" target="_blank">http://en.wikipedia.org/wiki/Switzerland</a>
</p>
EOF

        );
        $switzerlandTravel->setRawShortDescription(
            <<<EOF
<p>
Switzerland (German: Schweiz[note 3] [ˈʃvaɪts]; French: Suisse [sɥis]; Italian: Svizzera [ˈzvittsera]; Romansh: Svizra [ˈʒviːtsrɐ] or [ˈʒviːtsʁːɐ]), officially the Swiss Confederation (Latin: Confoederatio Helvetica, hence its abbreviation CH), is a federal parliamentary republic consisting of 26 cantons, with Bern as the seat of the federal authorities. The country is situated in Western and Central Europe,[note 4] where it is bordered by Germany to the north, France to the west, Italy to the south, and Austria and Liechtenstein to the east. Switzerland is a landlocked country geographically divided between the Alps, the Swiss Plateau and the Jura, spanning an area of 41,285 km2 (15,940 sq mi).

References - <a href="http://en.wikipedia.org/wiki/Switzerland" target="_blank">http://en.wikipedia.org/wiki/Switzerland</a>
</p>
EOF

        );
        $switzerlandTravel->setDescriptionFormatter('richhtml');
        $switzerlandTravel->setShortDescriptionFormatter('richhtml');
        $switzerlandTravel->setPrice(475);
        $switzerlandTravel->setStock(50);
        $switzerlandTravel->setVatRate(10);
        $switzerlandTravel->setTravelDate(new \DateTime('2015-09-26'));
        $switzerlandTravel->setEnabled(true);

        $manager->persist($switzerlandTravel);
        $this->setReference('travel_switzerland_product', $switzerlandTravel);

        $this->addMediaToProduct(__DIR__.'/../data/files/sylvain-switzerland/switzerland_2012-05-19_006.jpg', 'Switzerland Travel', 'Switzerland Travel', $switzerlandTravel);
        $this->addSwitzerlandGallery($switzerlandTravel);
        $this->addProductToCategory($switzerlandTravel, $travelsCategory, $manager);
        $this->addProductToCategory($switzerlandTravel, $this->getReference('travels_europe_category'), $manager);
        $this->addProductToCategory($switzerlandTravel, $this->getReference('travels_switzerland_category'), $manager);
        $this->addProductDeliveries($switzerlandTravel, $manager);
        $this->addProductToCollection($switzerlandTravel, $travelCollection, $manager);
        $this->addPackageToProduct($switzerlandTravel, $manager);

        $travelProvider = $productPool->getProvider($switzerlandTravel);

        // London tour small group variation
        $switzerlandSmallTravel = $this->generateDefaultTravelVariation($travelProvider, $switzerlandTravel);
        $switzerlandSmallTravel->setName('Switzerland tour for small group');
        $switzerlandSmallTravel->setSku('travel-switzerland-tour-5');
        $switzerlandSmallTravel->setSlug('travel-switzerland-tour-5');
        $switzerlandSmallTravel->setPriceIncludingVat(false);
        $switzerlandSmallTravel->setPrice(475);
        $switzerlandSmallTravel->setTravellers(5);
        $switzerlandSmallTravel->setTravelDate(new \DateTime('2015-09-26'));
        $switzerlandSmallTravel->setTravelDays(7);
        $switzerlandSmallTravel->setStock(50);

        $manager->persist($switzerlandSmallTravel);
        $this->setReference('travel_switzerland_small_product', $switzerlandSmallTravel);

        // London tour medium group variation
        $switzerlandMediumTravel = $this->generateDefaultTravelVariation($travelProvider, $switzerlandTravel);
        $switzerlandMediumTravel->setName('Switzerland tour for medium group');
        $switzerlandMediumTravel->setSku('travel-switzerland-tour-7');
        $switzerlandMediumTravel->setSlug('travel-switzerland-tour-7');
        $switzerlandMediumTravel->setPriceIncludingVat(false);
        $switzerlandMediumTravel->setPrice(750);
        $switzerlandMediumTravel->setTravellers(7);
        $switzerlandMediumTravel->setTravelDate(new \DateTime('2015-09-26'));
        $switzerlandMediumTravel->setTravelDays(7);
        $switzerlandMediumTravel->setStock(50);

        $manager->persist($switzerlandMediumTravel);
        $this->setReference('travel_switzerland_medium_product', $switzerlandMediumTravel);

        // London tour large group variation
        $switzerlandLargeTravel = $this->generateDefaultTravelVariation($travelProvider, $switzerlandTravel);
        $switzerlandLargeTravel->setName('Switzerland tour for large group');
        $switzerlandLargeTravel->setSku('travel-switzerland-tour-9');
        $switzerlandLargeTravel->setSlug('travel-switzerland-tour-9');
        $switzerlandLargeTravel->setPriceIncludingVat(false);
        $switzerlandLargeTravel->setPrice(875);
        $switzerlandLargeTravel->setTravellers(9);
        $switzerlandLargeTravel->setTravelDate(new \DateTime('2015-09-26'));
        $switzerlandLargeTravel->setTravelDays(7);
        $switzerlandLargeTravel->setStock(50);

        $manager->persist($switzerlandLargeTravel);
        $this->setReference('travel_switzerland_large_product', $switzerlandLargeTravel);

        // London tour extra-large group variation
        $switzerlandExtraLargeTravel = $this->generateDefaultTravelVariation($travelProvider, $switzerlandTravel);
        $switzerlandExtraLargeTravel->setName('Switzerland tour for extra-large group');
        $switzerlandExtraLargeTravel->setSku('travel-switzerland-tour-12');
        $switzerlandExtraLargeTravel->setSlug('travel-switzerland-tour-12');
        $switzerlandExtraLargeTravel->setPriceIncludingVat(false);
        $switzerlandExtraLargeTravel->setPrice(1000);
        $switzerlandExtraLargeTravel->setTravellers(12);
        $switzerlandExtraLargeTravel->setTravelDate(new \DateTime('2015-09-26'));
        $switzerlandExtraLargeTravel->setTravelDays(7);
        $switzerlandExtraLargeTravel->setStock(50);

        $manager->persist($switzerlandExtraLargeTravel);
        $this->setReference('travel_switzerland_extra_large_product', $switzerlandExtraLargeTravel);

        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 12;
    }

    protected function getLorem()
    {
        return "
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quae cum essent dicta, finem fecimus et ambulandi et disputandi. Duo Reges: constructio interrete. Itaque eos id agere, ut a se dolores, morbos, debilitates repellant. Quae cum dixisset paulumque institisset, Quid est? At iam decimum annum in spelunca iacet. Tibi hoc incredibile, quod beatissimum. Conferam avum tuum Drusum cum C. </p>

<p>Si quicquam extra virtutem habeatur in bonis. Idemque diviserunt naturam hominis in animum et corpus. Aliter homines, aliter philosophos loqui putas oportere? Videmusne ut pueri ne verberibus quidem a contemplandis rebus perquirendisque deterreantur? Cur ipse Pythagoras et Aegyptum lustravit et Persarum magos adiit? Sed ille, ut dixi, vitiose. </p>

<p>Atque his de rebus et splendida est eorum et illustris oratio. Ergo illi intellegunt quid Epicurus dicat, ego non intellego? Quamquam ab iis philosophiam et omnes ingenuas disciplinas habemus; Videamus animi partes, quarum est conspectus illustrior; Non est igitur summum malum dolor. </p>
        ";
    }

    /**
     * Create a ProductCategory and adds given Product to given Category.
     *
     * @param ProductInterface  $product
     * @param CategoryInterface $category
     * @param bool              $enabled
     * @param ObjectManager     $manager
     */
    protected function addProductToCategory(ProductInterface $product, CategoryInterface $category, ObjectManager $manager, $enabled = true)
    {
        $productCategory = new ProductCategory();

        $productCategory->setEnabled($enabled);
        $productCategory->setProduct($product);
        $productCategory->setCategory($category);

        if (!$product->hasOneMainCategory()) {
            $productCategory->setMain(true);
        }

        $product->addProductCategory($productCategory);

        $manager->persist($productCategory);
    }

    /**
     * Create a ProductCollection and adds given Product to given Collection.
     *
     * @param ProductInterface    $product
     * @param CollectionInterface $collection
     * @param bool                $enabled
     * @param ObjectManager       $manager
     */
    protected function addProductToCollection(ProductInterface $product, CollectionInterface $collection, ObjectManager $manager, $enabled = true)
    {
        $productCollection = new ProductCollection();

        $productCollection->setEnabled($enabled);
        $productCollection->setProduct($product);
        $productCollection->setCollection($collection);

        $product->addProductCollection($productCollection);

        $manager->persist($productCollection);
    }

    /**
     * Create and add deliveries for a given Product.
     *
     * @param ProductInterface $product
     * @param ObjectManager    $manager
     */
    protected function addProductDeliveries(ProductInterface $product, ObjectManager $manager)
    {
        $delivery = new Delivery();
        $delivery->setCountryCode('FR');
        $delivery->setCode('free');
        $delivery->setEnabled(true);
        $delivery->setPerItem(0);
        $delivery->setProduct($product);
        $product->addDelivery($delivery);
        $manager->persist($delivery);

        $delivery = new Delivery();
        $delivery->setCountryCode('GB');
        $delivery->setCode('free');
        $delivery->setEnabled(true);
        $delivery->setPerItem(0);
        $delivery->setProduct($product);
        $product->addDelivery($delivery);
        $manager->persist($delivery);

        $delivery = new Delivery();
        $delivery->setCountryCode('FR');
        $delivery->setCode('chronopost');
        $delivery->setEnabled(true);
        $delivery->setPerItem(rand(15, 30));
        $delivery->setProduct($product);
        $product->addDelivery($delivery);
        $manager->persist($delivery);

        $delivery = new Delivery();
        $delivery->setCountryCode('GB');
        $delivery->setCode('ups');
        $delivery->setEnabled(true);
        $delivery->setPerItem(rand(15, 30));
        $delivery->setProduct($product);
        $product->addDelivery($delivery);
        $manager->persist($delivery);
    }

    /**
     * Creates a media and add it to given product instance
     *
     * @param string           $mediaFilename A media filename
     * @param string           $name          A media name to set on creation
     * @param string           $description   A media description text
     * @param ProductInterface $product       A Product instance to add media
     * @param string           $author        A media author text
     * @param string           $copyright     A media copyright text
     */
    protected function addMediaToProduct($mediaFilename, $name, $description, ProductInterface $product, $author = null, $copyright = null)
    {
        $product->setImage($this->createMedia($mediaFilename, $name, $description, $author, $copyright));
    }

    /**
     * @param string $mediaFilename
     * @param string $name
     * @param string $description
     * @param string $author
     * @param string $copyright
     *
     * @return MediaInterface
     */
    protected function createMedia($mediaFilename, $name, $description, $author = null, $copyright = null)
    {
        $mediaManager = $this->getMediaManager();

        $file = new \SplFileInfo($mediaFilename);

        $media = $mediaManager->create();
        $media->setBinaryContent($file);
        $media->setEnabled(true);
        $media->setName($name);
        $media->setDescription($description);
        $media->setAuthorName($author);
        $media->setCopyright($copyright);

        $mediaManager->save($media, 'sonata_product', 'sonata.media.provider.image');

        return $media;
    }

    /**
     * Returns Switzerland gallery from a specified directory
     *
     * @param ProductInterface $product
     */
    protected function addSwitzerlandGallery(ProductInterface $product)
    {
        $gallery = $this->getGalleryManager()->create();
        $gallery->setName('Switzerland');
        $gallery->setContext('sonata_product');
        $gallery->setDefaultFormat('preview');
        $gallery->setEnabled(true);

        $files = Finder::create()
            ->name('*.jpg')
            ->in(__DIR__.'/../data/files/sylvain-switzerland');

        foreach ($files as $pos => $file) {
            $media = $this->getMediaManager()->create();
            $media->setBinaryContent($file);
            $media->setEnabled(true);
            $media->setDescription('Switzerland');
            $media->setName('Switzerland');
            $media->setAuthorName('Sylvain Deloux');
            $media->setCopyright('CC BY-NC-SA 4.0');

            $this->getMediaManager()->save($media, 'sonata_product', 'sonata.media.provider.image');

            $galleryHasMedia = new GalleryHasMedia();
            $galleryHasMedia->setMedia($media);
            $galleryHasMedia->setPosition($pos+1);
            $galleryHasMedia->setEnabled(true);

            $gallery->addGalleryHasMedias($galleryHasMedia);
        }

        $this->getGalleryManager()->update($gallery);

        $product->setGallery($gallery);
    }

    /**
     * Returns Paris gallery from a specified directory
     *
     * @param ProductInterface $product
     */
    protected function addParisGallery(ProductInterface $product)
    {
        $gallery = $this->getGalleryManager()->create();
        $gallery->setName('Paris');
        $gallery->setContext('sonata_product');
        $gallery->setDefaultFormat('preview');
        $gallery->setEnabled(true);

        $files = Finder::create()
            ->name('*.jpg')
            ->in(__DIR__.'/../data/files/gilles-paris');

        foreach ($files as $pos => $file) {
            $media = $this->getMediaManager()->create();
            $media->setBinaryContent($file);
            $media->setEnabled(true);
            $media->setDescription('Paris');
            $media->setName('Paris');
            $media->setAuthorName('Gilles Rosenbaum');
            $media->setCopyright('CC BY-NC-SA 4.0');

            $this->getMediaManager()->save($media, 'sonata_product', 'sonata.media.provider.image');

            $galleryHasMedia = new GalleryHasMedia();
            $galleryHasMedia->setMedia($media);
            $galleryHasMedia->setPosition($pos+1);
            $galleryHasMedia->setEnabled(true);

            $gallery->addGalleryHasMedias($galleryHasMedia);
        }

        $files = Finder::create()
            ->name('*.jpg')
            ->in(__DIR__.'/../data/files/hugo-paris');

        foreach ($files as $pos => $file) {
            $media = $this->getMediaManager()->create();
            $media->setBinaryContent($file);
            $media->setEnabled(true);
            $media->setDescription('Paris');
            $media->setName('Paris');
            $media->setAuthorName('Hugo Briand');
            $media->setCopyright("Je soussigné Hugo Briand donne l'autorisation à Sonata-Project d'utiliser mes photos comme bon lui semblera");

            $this->getMediaManager()->save($media, 'sonata_product', 'sonata.media.provider.image');

            $galleryHasMedia = new GalleryHasMedia();
            $galleryHasMedia->setMedia($media);
            $galleryHasMedia->setPosition($pos+1);
            $galleryHasMedia->setEnabled(true);

            $gallery->addGalleryHasMedias($galleryHasMedia);
        }

        $this->getGalleryManager()->update($gallery);

        $product->setGallery($gallery);
    }

    /**
     * Returns Canada gallery from a specified directory
     *
     * @param ProductInterface $product
     */
    protected function addCanadaGallery(ProductInterface $product)
    {
        $gallery = $this->getGalleryManager()->create();
        $gallery->setName('Canada');
        $gallery->setContext('sonata_product');
        $gallery->setDefaultFormat('preview');
        $gallery->setEnabled(true);

        $files = Finder::create()
            ->name('*.jpg')
            ->in(__DIR__.'/../data/files/gilles-canada');

        foreach ($files as $pos => $file) {
            $media = $this->getMediaManager()->create();
            $media->setBinaryContent($file);
            $media->setEnabled(true);
            $media->setDescription('Canada');
            $media->setName('Canada');
            $media->setAuthorName('Gilles Rosenbaum');
            $media->setCopyright('CC BY-NC-SA 4.0');

            $this->getMediaManager()->save($media, 'sonata_product', 'sonata.media.provider.image');

            $galleryHasMedia = new GalleryHasMedia();
            $galleryHasMedia->setMedia($media);
            $galleryHasMedia->setPosition($pos+1);
            $galleryHasMedia->setEnabled(true);

            $gallery->addGalleryHasMedias($galleryHasMedia);
        }

        $files = Finder::create()
            ->name('*.jpg')
            ->in(__DIR__.'/../data/files/hugo-canada');

        foreach ($files as $pos => $file) {
            $media = $this->getMediaManager()->create();
            $media->setBinaryContent($file);
            $media->setEnabled(true);
            $media->setDescription('Canada');
            $media->setName('Canada');
            $media->setAuthorName('Hugo Briand');
            $media->setCopyright("Je soussigné Hugo Briand donne l'autorisation à Sonata-Project d'utiliser mes photos comme bon lui semblera");

            $this->getMediaManager()->save($media, 'sonata_product', 'sonata.media.provider.image');

            $galleryHasMedia = new GalleryHasMedia();
            $galleryHasMedia->setMedia($media);
            $galleryHasMedia->setPosition($pos+1);
            $galleryHasMedia->setEnabled(true);

            $gallery->addGalleryHasMedias($galleryHasMedia);
        }

        $this->getGalleryManager()->update($gallery);

        $product->setGallery($gallery);
    }

    /**
     * Returns Japan gallery from a specified directory
     *
     * @param ProductInterface $product
     */
    protected function addJapanGallery(ProductInterface $product)
    {
        $gallery = $this->getGalleryManager()->create();
        $gallery->setName('Japan');
        $gallery->setContext('sonata_product');
        $gallery->setDefaultFormat('preview');
        $gallery->setEnabled(true);

        $files = Finder::create()
            ->name('*.jpg')
            ->in(__DIR__.'/../data/files/maha-japan');

        foreach ($files as $pos => $file) {
            $media = $this->getMediaManager()->create();
            $media->setBinaryContent($file);
            $media->setEnabled(true);
            $media->setDescription('Japan');
            $media->setName('Japan');
            $media->setAuthorName('Maha Kanas');
            $media->setCopyright("CC BY-NC-SA 4.0");

            $this->getMediaManager()->save($media, 'sonata_product', 'sonata.media.provider.image');

            $galleryHasMedia = new GalleryHasMedia();
            $galleryHasMedia->setMedia($media);
            $galleryHasMedia->setPosition($pos+1);
            $galleryHasMedia->setEnabled(true);

            $gallery->addGalleryHasMedias($galleryHasMedia);
        }

        $this->getGalleryManager()->update($gallery);

        $product->setGallery($gallery);
    }

    /**
     * @param ProductInterface $product
     *
     * @return object|\Sonata\MediaBundle\Model\MediaInterface
     */
    protected function getGalleryForProduct(ProductInterface $product)
    {
        $galleryReference = sprintf("gallery_%s", $product->getSku());

        if ($this->hasReference($galleryReference)) {
            return $this->getReference($galleryReference);
        }

        $gallery = $this->getGalleryManager()->create();

        $gallery->setName($product->getSlug());
        $gallery->setEnabled(true);
        $gallery->setDefaultFormat('sonata_product_preview');
        $gallery->setContext('sonata_product');

        $this->setReference($galleryReference, $gallery);

        $this->getGalleryManager()->update($gallery);

        $product->setGallery($gallery);

        return $gallery;
    }

    /**
     * @param MediaInterface   $media
     * @param GalleryInterface $gallery
     */
    protected function addMediaToGallery(MediaInterface $media, GalleryInterface $gallery)
    {
        $galleryHasMedia = new GalleryHasMedia();
        $galleryHasMedia->setMedia($media);
        $galleryHasMedia->setPosition(count($gallery->getGalleryHasMedias()) + 1);
        $galleryHasMedia->setEnabled(true);

        $gallery->addGalleryHasMedias($galleryHasMedia);
    }

    /**
     * @param ProductInterface $product
     * @param ObjectManager    $manager
     */
    protected function addPackageToProduct(ProductInterface $product, ObjectManager $manager)
    {
        $package = new Package();

        $package->setProduct($product);
        $package->setWidth(rand(1, 50));
        $package->setHeight(rand(1, 50));
        $package->setLength(rand(1, 50));
        $package->setWeight(rand(1, 50));
        $package->setEnabled(true);
        $package->setCreatedAt(new \DateTime());
        $package->setUpdatedAt(new \DateTime());

        $product->addPackage($package);

        $manager->persist($package);
    }

    /**
     * Returns the Dummy category.
     *
     * @return CategoryInterface
     */
    protected function getDummyCategory()
    {
        return $this->getReference('dummy_category');
    }

    /**
     * Returns the Goodies category.
     *
     * @return CategoryInterface
     */
    protected function getGoodiesCategory()
    {
        return $this->getReference('goodies_category');
    }

    /**
     * Returns the Travels category.
     *
     * @return CategoryInterface
     */
    protected function getTravelsCategory()
    {
        return $this->getReference('travels_category');
    }

    /**
     * Returns the plush sub-Category.
     *
     * @return CategoryInterface
     */
    protected function getPlushesCategory()
    {
        return $this->getReference('plushes_goodies_category');
    }

    /**
     * Returns the mugs sub-Category.
     *
     * @return CategoryInterface
     */
    protected function getMugCategory()
    {
        return $this->getReference('sonata_mugs_category');
    }

    /**
     * Returns the clothes sub-Category.
     *
     * @return CategoryInterface
     */
    protected function getClothesCategory()
    {
        return $this->getReference('sonata_clothes_category');
    }

    /**
     * Returns the shoes sub-Category.
     *
     * @return CategoryInterface
     */
    protected function getShoesCategory()
    {
        return $this->getReference('sonata_shoes_category');
    }

    /**
     * Returns the PHP collection
     *
     * @return CollectionInterface
     */
    protected function getPhpCollection()
    {
        return $this->getReference('php_collection');
    }

    /**
     * Returns the Travel collection
     *
     * @return CollectionInterface
     */
    protected function getTravelCollection()
    {
        return $this->getReference('travel_collection');
    }

    /**
     * Returns the Dummy collection
     *
     * @return CollectionInterface
     */
    protected function getDummyCollection()
    {
        return $this->getReference('dummy_collection');
    }

    /**
     * Returns the Sonata MediaManager.
     *
     * @return \Sonata\MediaBundle\Model\MediaManagerInterface
     */
    public function getMediaManager()
    {
        return $this->container->get('sonata.media.manager.media');
    }

    /**
     * Return the Product Pool.
     *
     * @return \Sonata\Component\Product\Pool
     */
    protected function getProductPool()
    {
        return $this->container->get('sonata.product.pool');
    }

    /**
     * @return \Sonata\MediaBundle\Model\GalleryManagerInterface
     */
    public function getGalleryManager()
    {
        return $this->container->get('sonata.media.manager.gallery');
    }

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Returns a new travel variation entity
     *
     * @param \Sonata\Component\Product\ProductProviderInterface $provider
     * @param \Sonata\Component\Product\ProductInterface $parent
     *
     * @return Travel
     */
    protected function generateDefaultTravelVariation($provider, $parent)
    {
        $entity = $provider->createVariation($parent);
        $entity->setEnabled(true);

        return $entity;
    }
}
