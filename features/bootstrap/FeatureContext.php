<?php

use Behat\Behat\Context\BehatContext;

/**
 * Features context.
 */
class FeatureContext extends BehatContext
{
    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @param   array   $parameters     context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
        $this->useContext('browser', new \BrowserContext($parameters));
    }

    /**
     * Setup XHR headers
     *
     * @Given /^I am an XHR request$/
     *
     */
    public function iAmAnXHRRequest()
    {
        $this->getSubcontext("browser")->getSession()->setRequestHeader("X-Requested-With", "XMLHttpRequest");
    }

    /**
     * @Given /^the response is JSON$/
     */
    public function theResponseIsJson()
    {
        $data = json_decode($this->getSubcontext("browser")->getSession()->getPage()->getContent());

        if (empty($data)) {
            throw new Exception("Response was not JSON\n" . $this->getSubcontext("browser")->getSession()->getPage()->getContent());
        }
    }

    /**
     * @Given /^the price is (\d+)$/
     */
    public function thePriceIs($price)
    {
        $data = json_decode($this->getSubcontext("browser")->getSession()->getPage()->getContent(), true);

        if ((int) $price !== $data['price']) {
            throw new Exception("The price was not ".$price.", it was ".$data['price']);
        }
    }

    /**
     * @Given /^the stock is (\d+)$/
     */
    public function theStockIs($stock)
    {
        $data = json_decode($this->getSubcontext("browser")->getSession()->getPage()->getContent(), true);

        if ((int) $stock !== $data['stock']) {
            throw new Exception("The stock was not ".$stock.", it was ".$data['stock']);
        }
    }
}
