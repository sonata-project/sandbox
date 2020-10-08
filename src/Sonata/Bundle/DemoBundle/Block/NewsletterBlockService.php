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

namespace Sonata\Bundle\DemoBundle\Block;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\BlockBundle\Block\BaseBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\Bundle\DemoBundle\Form\Type\NewsletterType;
use Sonata\Form\Validator\ErrorElement;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class NewsletterBlockService.
 *
 * Renders a fake newsletter block for the sandbox
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
final class NewsletterBlockService extends BaseBlockService
{
    /**
     * @var FormInterface
     */
    protected $form;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * Constructor.
     *
     * @param string               $name        A block name
     * @param EngineInterface      $templating  Twig engine service
     * @param FormFactoryInterface $formFactory Symfony FormFactory service
     */
    public function __construct(string $name, EngineInterface $templating, FormFactoryInterface $formFactory)
    {
        parent::__construct($name, $templating);

        $this->formFactory = $formFactory;
    }

    public function execute(BlockContextInterface $blockContext, ?Response $response = null): Response
    {
        $this->form = $this->formFactory->create($blockContext->getSetting('form_type'));

        return $this->renderPrivateResponse($blockContext->getTemplate(), [
            'block' => $blockContext->getBlock(),
            'context' => $blockContext,
            'form' => $this->form->createView(),
        ]);
    }

    public function buildEditForm(FormMapper $form, BlockInterface $block): void
    {
        // no options available
    }

    public function validateBlock(ErrorElement $errorElement, BlockInterface $block): void
    {
    }

    public function configureSettings(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'template' => '@SonataDemo/Block/newsletter.html.twig',
            'ttl' => 0,
            'form_type' => NewsletterType::class,
        ]);
    }

    public function getName(): string
    {
        return 'Newsletter Block (fake)';
    }
}
