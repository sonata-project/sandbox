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
     * @var array
     */
    private $identifiers = array();

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
        $this->useContext('api',     new \Behat\CommonContexts\WebApiContext($parameters['base_url']));
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
     * Sends HTTP request to specific URL using latest identifier.
     *
     * @param string $method request method
     * @param string $url    relative url
     *
     * @When /^(?:I )?send a ([A-Z]+) request to "([^"]+)" using last identifier:$/
     */
    public function iSendARequestUsingLastIdentifier($method, $url)
    {
        $this->sendRequestUsingLastIdentifier($method, $url);
    }

    /**
     * Sends HTTP request to specific URL with field values from Table and using latest identifier.
     *
     * @param string    $method request method
     * @param string    $url    relative url
     * @param TableNode $post   table of post values
     *
     * @When /^(?:I )?send a ([A-Z]+) request to "([^"]+)" using last identifier with values:$/
     */
    public function iSendARequestWithValuesUsingLastIdentifier($method, $url, TableNode $post)
    {
        $this->sendRequestUsingLastIdentifier($method, $url, $post);
    }

    /**
     * Sends a request using last identifier
     *
     * @param string    $method request method
     * @param string    $url    relative url
     * @param TableNode $post   table of post values
     */
    protected function sendRequestUsingLastIdentifier($method, $url, TableNode $post = null)
    {
        $url    = $this->baseUrl.'/'.ltrim($this->replaceIdentifiers($url), '/');
        $fields = array();

        if ($post) {
            foreach ($post->getRowsHash() as $key => $val) {
                if (preg_match('/^<(.*)>$/', $val)) {
                    $alias = str_replace(array('<', '>'), null, $val);
                    $val = isset($this->identifiers[$alias]) ? $this->identifiers[$alias] : $val;
                }

                $fields[$key] = $val;
            }
        }

        $headers = $this->getSubcontext('api')->getBrowser()->getLastRequest()->getHeaders();
        $url = str_replace('//api', '/api', $url);

        $this->getSubcontext('api')->getBrowser()->submit($url, $fields, $method, $headers);
    }

    /**
     * @Then /^store the XML response identifier as "(.*)"$/
     */
    public function storeTheResponseIdentifier($alias)
    {
        $responseContent = $this->getSubcontext('api')->getBrowser()->getLastResponse()->getContent();

        $data = simplexml_load_string($responseContent);

        if (false === $data) {
            throw new Exception(sprintf('Response was not XML : "%s"', $responseContent));
        }

        $identifier = $data->attributes()->id;

        if (null === $identifier) {
            $data = current($data);
            $identifier = $data->attributes()->id;
        }

        $this->identifiers[$alias] = current($identifier);
    }

    /**
     * Returns URL with last identifier stored in context
     *
     * @param string $url
     *
     * @return string
     */
    protected function replaceIdentifiers($url)
    {
        preg_match_all('/<(.*)>/U', $url, $matches);

        if (isset($matches[1])) {
            foreach ($matches[1] as $alias) {
                if (isset($this->identifiers[$alias])) {
                    $url = str_replace(sprintf('<%s>', $alias), $this->identifiers[$alias], $url);
                }
            }
        }

        return $url;
    }
}
