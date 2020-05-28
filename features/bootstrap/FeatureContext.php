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

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;

/**
 * Features context.
 */
class FeatureContext implements Context
{
    /**
     * @var BrowserContext
     */
    private $browserContext;

    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @param array $parameters context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
    }

    /**
     * @BeforeScenario
     */
    public function gatherContexts(BeforeScenarioScope $scope)
    {
        $environment = $scope->getEnvironment();

        $this->browserContext = $environment->getContext('BrowserContext');
    }

    /**
     * @BeforeScenario
     */
    public static function setupFeature(BeforeScenarioScope $scope)
    {
        include_once realpath(__DIR__.'/../CiHelper.php');

        CiHelper::run($scope);
    }

    /**
     * Setup XHR headers.
     *
     * @Given /^I am an XHR request$/
     */
    public function iAmAnXHRRequest()
    {
        $this->browserContext->getSession()->setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    }

    /**
     * @Given /^the response is JSON$/
     */
    public function theResponseIsJson()
    {
        $data = json_decode($this->browserContext->getSession()->getPage()->getContent());

        if (empty($data)) {
            throw new Exception("Response was not JSON\n".$this->browserContext->getSession()->getPage()->getContent());
        }
    }

    /**
     * @Given /^the price is ([0-9]*(\.[0-9]*)?)$/
     */
    public function thePriceIs($price)
    {
        $data = json_decode($this->browserContext->getSession()->getPage()->getContent(), true);

        if ((float) $price !== (float) $data['price']) {
            throw new Exception('The price was not '.$price.', it was '.$data['price']);
        }
    }

    /**
     * @Given /^the stock is (\d+)$/
     */
    public function theStockIs($stock)
    {
        $data = json_decode($this->browserContext->getSession()->getPage()->getContent(), true);

        if ((int) $stock !== $data['stock']) {
            throw new Exception('The stock was not '.$stock.', it was '.$data['stock']);
        }
    }

    /**
     * @Given /^the variation_url is "(.*)"$/
     */
    public function theVariationUrlIs($variationUrl)
    {
        $data = json_decode($this->browserContext->getSession()->getPage()->getContent(), true);

        if ($variationUrl !== $data['variation_url']) {
            throw new Exception('The variation_url was not '.$variationUrl.', it was '.$data['variation_url']);
        }
    }

    /**
     * @Given /^the error is "(.*)"$/
     */
    public function theErrorIs($error)
    {
        $data = json_decode($this->browserContext->getSession()->getPage()->getContent(), true);

        if ($error !== $data['error']) {
            throw new Exception('The error was not '.$error.', it was '.$data['error']);
        }
    }
}
