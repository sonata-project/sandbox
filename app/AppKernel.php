<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function init()
    {
        // Please read http://symfony.com/doc/2.0/book/installation.html#configuration-and-setup

        parent::init();
    }

    public function registerBundles()
    {
        $bundles = array(
            // SYMFONY STANDARD EDITION
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new JMS\AopBundle\JMSAopBundle(),
            new JMS\SecurityExtraBundle\JMSSecurityExtraBundle(),

            // DOCTRINE
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),

            // KNP HELPER BUNDLES
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new Knp\Bundle\MarkdownBundle\KnpMarkdownBundle(),
            new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),

            // USER
            new FOS\UserBundle\FOSUserBundle(),
            new Sonata\UserBundle\SonataUserBundle('FOSUserBundle'),
            new Application\Sonata\UserBundle\ApplicationSonataUserBundle(),

            // PAGE
            new Sonata\PageBundle\SonataPageBundle(),
            new Application\Sonata\PageBundle\ApplicationSonataPageBundle(),

            // NEWS
            new Sonata\MarkItUpBundle\SonataMarkItUpBundle(),
            new Ivory\CKEditorBundle\IvoryCKEditorBundle(),
            new Sonata\NewsBundle\SonataNewsBundle(),
            new Application\Sonata\NewsBundle\ApplicationSonataNewsBundle(),

            // MEDIA
            new Sonata\MediaBundle\SonataMediaBundle(),
            new Application\Sonata\MediaBundle\ApplicationSonataMediaBundle(),
            // new Liip\ImagineBundle\LiipImagineBundle(),

            // E-COMMERCE
            new Sonata\BasketBundle\SonataBasketBundle(),
            new Application\Sonata\BasketBundle\ApplicationSonataBasketBundle(),
            new Sonata\CustomerBundle\SonataCustomerBundle(),
            new Application\Sonata\CustomerBundle\ApplicationSonataCustomerBundle(),
            new Sonata\DeliveryBundle\SonataDeliveryBundle(),
            new Application\Sonata\DeliveryBundle\ApplicationSonataDeliveryBundle(),
            new Sonata\InvoiceBundle\SonataInvoiceBundle(),
            new Application\Sonata\InvoiceBundle\ApplicationSonataInvoiceBundle(),
            new Sonata\OrderBundle\SonataOrderBundle(),
            new Application\Sonata\OrderBundle\ApplicationSonataOrderBundle(),
            new Sonata\PaymentBundle\SonataPaymentBundle(),
            new Application\Sonata\PaymentBundle\ApplicationSonataPaymentBundle(),
            new Sonata\ProductBundle\SonataProductBundle(),
            new Application\Sonata\ProductBundle\ApplicationSonataProductBundle(),
            new Sonata\PriceBundle\SonataPriceBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle($this),

            // SONATA CORE & HELPER BUNDLES
            new Sonata\EasyExtendsBundle\SonataEasyExtendsBundle(),
            new Sonata\jQueryBundle\SonatajQueryBundle(),
            new Sonata\AdminBundle\SonataAdminBundle(),
            new Sonata\CoreBundle\SonataCoreBundle(),
            new Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle(),
            new Sonata\IntlBundle\SonataIntlBundle(),
            new Sonata\FormatterBundle\SonataFormatterBundle(),
            new Sonata\CacheBundle\SonataCacheBundle(),
            new Sonata\BlockBundle\SonataBlockBundle(),
            new Sonata\SeoBundle\SonataSeoBundle(),
            new Sonata\ClassificationBundle\SonataClassificationBundle(),
            new Application\Sonata\ClassificationBundle\ApplicationSonataClassificationBundle(),
            new Sonata\NotificationBundle\SonataNotificationBundle(),
            new Application\Sonata\NotificationBundle\ApplicationSonataNotificationBundle(),
            new Application\Sonata\AdminBundle\ApplicationSonataAdminBundle(),

            // CMF Integration
            new Symfony\Cmf\Bundle\RoutingBundle\CmfRoutingBundle(),

            // Liip Bundles
//            new Liip\ImagineBundle\LiipImagineBundle(),

            // Bootstrap
            new Mopa\Bundle\BootstrapBundle\MopaBootstrapBundle(),

            // DEMO and QA - Can be deleted
            new Sonata\Bundle\DemoBundle\SonataDemoBundle(),
            new Sonata\Bundle\QABundle\SonataQABundle(),

            // Disable this if you don't want the audit on entities
            new SimpleThings\EntityAudit\SimpleThingsEntityAuditBundle(),

            // Disable this if you don't want the timeline in the admin
            new Spy\TimelineBundle\SpyTimelineBundle(),
            new Sonata\TimelineBundle\SonataTimelineBundle(),
            new Application\Sonata\TimelineBundle\ApplicationSonataTimelineBundle() // easy extends integration
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
            $bundles[] = new Bazinga\Bundle\FakerBundle\BazingaFakerBundle();
            $bundles[] = new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle();
        }

        return $bundles;
    }

    /**
     * @param Symfony\Component\Config\Loader\LoaderInterface $loader
     * @return void
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
