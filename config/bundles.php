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

return [
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
    Symfony\Bundle\SecurityBundle\SecurityBundle::class => ['all' => true],
    Symfony\Bundle\TwigBundle\TwigBundle::class => ['all' => true],
    Symfony\Bundle\MonologBundle\MonologBundle::class => ['all' => true],
    Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle::class => ['all' => true],
    Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle::class => ['all' => true],
    Doctrine\Bundle\DoctrineCacheBundle\DoctrineCacheBundle::class => ['all' => true],
    Doctrine\Bundle\DoctrineBundle\DoctrineBundle::class => ['all' => true],
    Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle::class => ['dev' => true, 'test' => true],
    Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle::class => ['all' => true],
    Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle::class => ['all' => true],
    Knp\Bundle\MenuBundle\KnpMenuBundle::class => ['all' => true],
    Knp\Bundle\MarkdownBundle\KnpMarkdownBundle::class => ['all' => true],
    Knp\Bundle\PaginatorBundle\KnpPaginatorBundle::class => ['all' => true],
    FOS\UserBundle\FOSUserBundle::class => ['all' => true],
    Sonata\UserBundle\SonataUserBundle::class => ['all' => true],
    Sonata\PageBundle\SonataPageBundle::class => ['all' => true],
    Sonata\NewsBundle\SonataNewsBundle::class => ['all' => true],
    Sonata\MediaBundle\SonataMediaBundle::class => ['all' => true],
    FOS\CKEditorBundle\FOSCKEditorBundle::class => ['all' => true],
    Sonata\AdminBundle\SonataAdminBundle::class => ['all' => true],
    Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle::class => ['all' => true],
    FOS\RestBundle\FOSRestBundle::class => ['all' => true],
    Nelmio\ApiDocBundle\NelmioApiDocBundle::class => ['all' => true],
    Sonata\BasketBundle\SonataBasketBundle::class => ['all' => true],
    Sonata\CustomerBundle\SonataCustomerBundle::class => ['all' => true],
    Sonata\DeliveryBundle\SonataDeliveryBundle::class => ['all' => true],
    Sonata\InvoiceBundle\SonataInvoiceBundle::class => ['all' => true],
    Sonata\OrderBundle\SonataOrderBundle::class => ['all' => true],
    Sonata\PaymentBundle\SonataPaymentBundle::class => ['all' => true],
    Sonata\ProductBundle\SonataProductBundle::class => ['all' => true],
    Sonata\PriceBundle\SonataPriceBundle::class => ['all' => true],
    JMS\SerializerBundle\JMSSerializerBundle::class => ['all' => true],
    FOS\CommentBundle\FOSCommentBundle::class => ['all' => true],
    Sonata\CommentBundle\SonataCommentBundle::class => ['all' => true],
    Sonata\EasyExtendsBundle\SonataEasyExtendsBundle::class => ['all' => true],
    Sonata\IntlBundle\SonataIntlBundle::class => ['all' => true],
    Sonata\FormatterBundle\SonataFormatterBundle::class => ['all' => true],
    Sonata\CacheBundle\SonataCacheBundle::class => ['all' => true],
    Sonata\BlockBundle\SonataBlockBundle::class => ['all' => true],
    Sonata\SeoBundle\SonataSeoBundle::class => ['all' => true],
    Sonata\ClassificationBundle\SonataClassificationBundle::class => ['all' => true],
    Sonata\NotificationBundle\SonataNotificationBundle::class => ['all' => true],
    Sonata\DatagridBundle\SonataDatagridBundle::class => ['all' => true],
    Symfony\Cmf\Bundle\RoutingBundle\CmfRoutingBundle::class => ['all' => true],
    Sonata\Bundle\DemoBundle\SonataDemoBundle::class => ['all' => true],
    Sonata\Bundle\QABundle\SonataQABundle::class => ['all' => true],
    Spy\TimelineBundle\SpyTimelineBundle::class => ['all' => true],
    Sonata\TimelineBundle\SonataTimelineBundle::class => ['all' => true],
    Symfony\Bundle\AclBundle\AclBundle::class => ['all' => true],
    Symfony\Bundle\DebugBundle\DebugBundle::class => ['dev' => true, 'test' => true],
    Bazinga\Bundle\FakerBundle\BazingaFakerBundle::class => ['all' => true],
    Symfony\Bundle\WebProfilerBundle\WebProfilerBundle::class => ['dev' => true, 'test' => true],
    AppBundle\AppBundle::class => ['all' => true],
    Mopa\Bundle\BootstrapBundle\MopaBootstrapBundle::class => ['all' => true],
    Sonata\Doctrine\Bridge\Symfony\Bundle\SonataDoctrineBundle::class => ['all' => true],
    Sonata\Form\Bridge\Symfony\SonataFormBundle::class => ['all' => true],
    Sonata\Twig\Bridge\Symfony\SonataTwigBundle::class => ['all' => true],
    Symfony\WebpackEncoreBundle\WebpackEncoreBundle::class => ['all' => true],
];
