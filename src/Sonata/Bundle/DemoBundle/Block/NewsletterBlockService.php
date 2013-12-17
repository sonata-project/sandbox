<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Sonata\Bundle\DemoBundle\Block;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\BlockBundle\Block\BaseBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Class NewsletterBlockService
 *
 * Renders a fake newsletter block for the sandbox
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class NewsletterBlockService extends BaseBlockService
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var FormInterface
     */
    protected $form;

    /**
     * Constructor
     *
     * @param string               $name        A block name
     * @param EngineInterface      $templating  Twig engine service
     * @param Request              $request     Symfony Request service
     * @param FormFactoryInterface $formFactory Symfony FormFactory service
     * @param string               $formType    Newsletter form type
     */
    public function __construct($name, EngineInterface $templating, Request $request, FormFactoryInterface $formFactory, $formType)
    {
        parent::__construct($name, $templating);

        $this->request = $request;
        $this->form    = $formFactory->create($formType);
    }

    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $message = null;

        if ($this->request->isMethod('POST')) {
            $this->form->handleRequest($this->request);

            if ($this->form->isValid()) {
                $message = 'Sorry, this is just a demonstration block, it does not really work.';
            }
        }

        return $this->renderPrivateResponse($blockContext->getTemplate(), array(
            'block'   => $blockContext->getBlock(),
            'context' => $blockContext,
            'form'    => $this->form->createView(),
            'message' => $message,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function buildEditForm(FormMapper $form, BlockInterface $block)
    {
        // no options available
    }

    /**
     * {@inheritdoc}
     */
    public function validateBlock(ErrorElement $errorElement, BlockInterface $block)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultSettings(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'template' => 'SonataDemoBundle:Block:newsletter.html.twig',
            'ttl'      => 0
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Newsletter Block (fake)';
    }
}