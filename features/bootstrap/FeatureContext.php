<?php

use Behat\Behat\Context\BehatContext;

use Behat\Gherkin\Node\TableNode;

/**
 * Features context.
 */
class FeatureContext extends BehatContext
{
    /**
     * @var string
     */
    private $baseUrl;

    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @param   array   $parameters     context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
        $this->baseUrl = $parameters['base_url'];

        $this->useContext('browser', new \BrowserContext($parameters));
        $this->useContext('api',     new \ApiContext($parameters['base_url']));
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
     * @Given /^the price is ([0-9]*(\.[0-9]*)?)$/
     */
    public function thePriceIs($price)
    {
        $data = json_decode($this->getSubcontext("browser")->getSession()->getPage()->getContent(), true);

        if ((float) $price !== $data['price']) {
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

    /**
     * @Given /^the variation_url is "(.*)"$/
     */
    public function theVariationUrlIs($variationUrl)
    {
        $data = json_decode($this->getSubcontext("browser")->getSession()->getPage()->getContent(), true);

        if ($variationUrl !== $data['variation_url']) {
            throw new Exception("The variation_url was not ".$variationUrl.", it was ".$data['variation_url']);
        }
    }

    /**
     * @Given /^the error is "(.*)"$/
     */
    public function theErrorIs($error)
    {
        $data = json_decode($this->getSubcontext("browser")->getSession()->getPage()->getContent(), true);

        if ($error !== $data['error']) {
            throw new Exception("The error was not ".$error.", it was ".$data['error']);
        }
    }


    /**
     * @Given /^response should contain "([jJ][sS][oO][nN]|[xX][mM][lL])" object$/
     */
    public function theResponseShouldContainObject($objectType)
    {
        $responseContent = $this->getSubcontext('api')->getBrowser()->getLastResponse()->getContent();

        $objectType = strtolower($objectType);

        switch($objectType) {
            case 'xml':
                if (false === simplexml_load_string($responseContent)) {
                    throw new Exception(sprintf('Response was not xml : "%s"', $responseContent));
                }
                break;
            case 'json':
                if (null === json_decode($responseContent)) {
                    throw new Exception(sprintf('Response was not json : "%s"', $responseContent));
                }
                break;
        }
    }

    /**
     * @Given /^response should be a binary$/
     */
    public function theResponseShouldBeABinary()
    {
        /** @var \Behat\CommonContexts\WebApiContext $context */
        $context = $this->getSubcontext('api');
        /** @var \Guzzle\Http\Message\Response $response */
        $response = $context->getBrowser()->getLastResponse();

        if ("bytes" !== $response->getHeader('Accept-Ranges')) {
            throw new Exception(sprintf('Response Accept-Ranges header not bytes: "%s"', $response->getHeader('Accept-Ranges')));
        }

        if (false === strpos($response->getHeader('Content-Disposition'), 'attachment')) {
            throw new Exception(sprintf('Response Content-Disposition header not attachment: "%s"', $response->getHeader('Content-Disposition')));
        }
    }
}
