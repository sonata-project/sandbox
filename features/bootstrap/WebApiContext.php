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
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Buzz\Browser;
use Buzz\Message\Request;
use PHPUnit\Framework\Assert;

/**
 * Provides web API description definitions.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class WebApiContext implements Context
{
    private $browser;
    private $baseUrl;
    private $authorization;
    private $placeHolders = [];
    private $headers = [];

    /**
     * Initializes context.
     *
     * @param string  $baseUrl base API url
     * @param Browser $browser browser instance (optional)
     */
    public function __construct($baseUrl, Browser $browser = null)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        if (null === $browser) {
            $this->browser = new Browser();
        } else {
            $this->browser = $browser;
        }
    }

    /**
     * Adds Basic Authentication header to next request.
     *
     * @param string $username
     * @param string $password
     *
     * @Given /^I am authenticating as "([^"]*)" with "([^"]*)" password$/
     */
    public function iAmAuthenticatingAs($username, $password)
    {
        $this->removeHeader('Authorization');
        $this->authorization = base64_encode($username.':'.$password);
        $this->addHeader('Authorization: Basic '.$this->authorization);
    }

    /**
     * Sets a HTTP Header.
     *
     * @param string $name  header name
     * @param string $value header value
     *
     * @Given /^I set header "([^"]*)" with value "([^"]*)"$/
     */
    public function iSetHeaderWithValue($name, $value)
    {
        $this->addHeader($name.': '.$value);
    }

    /**
     * Sends HTTP request to specific relative URL.
     *
     * @param string $method request method
     * @param string $url    relative url
     *
     * @When /^(?:I )?send a ([A-Z]+) request to "([^"]+)"$/
     */
    public function iSendARequest($method, $url)
    {
        $url = $this->baseUrl.'/'.ltrim($this->replacePlaceHolder($url), '/');
        $this->browser->call($url, $method, $this->getHeaders());
    }

    /**
     * Sends HTTP request to specific URL with field values from Table.
     *
     * @param string    $method request method
     * @param string    $url    relative url
     * @param TableNode $post   table of post values
     *
     * @When /^(?:I )?send a ([A-Z]+) request to "([^"]+)" with values:$/
     */
    public function iSendARequestWithValues($method, $url, TableNode $post)
    {
        $url = $this->baseUrl.'/'.ltrim($this->replacePlaceHolder($url), '/');
        $fields = [];
        foreach ($post->getRowsHash() as $key => $val) {
            $fields[$key] = $this->replacePlaceHolder($val);
        }
        $this->browser->submit($url, $fields, $method, $this->getHeaders());
    }

    /**
     * Sends HTTP request to specific URL with raw body from PyString.
     *
     * @param string       $method request method
     * @param string       $url    relative url
     * @param PyStringNode $string request body
     *
     * @When /^(?:I )?send a ([A-Z]+) request to "([^"]+)" with body:$/
     */
    public function iSendARequestWithBody($method, $url, PyStringNode $string)
    {
        $url = $this->baseUrl.'/'.ltrim($this->replacePlaceHolder($url), '/');
        $string = $this->replacePlaceHolder(trim($string));
        $this->browser->call($url, $method, $this->getHeaders(), $string);
    }

    /**
     * Sends HTTP request to specific URL with form data from PyString.
     *
     * @param string       $method request method
     * @param string       $url    relative url
     * @param PyStringNode $string request body
     *
     * @When /^(?:I )?send a ([A-Z]+) request to "([^"]+)" with form data:$/
     */
    public function iSendARequestWithFormData($method, $url, PyStringNode $string)
    {
        $url = $this->baseUrl.'/'.ltrim($this->replacePlaceHolder($url), '/');
        $string = $this->replacePlaceHolder(trim($string));
        parse_str(implode('&', explode("\n", $string)), $fields);
        $this->browser->submit($url, $fields, $method, $this->getHeaders());
    }

    /**
     * Checks that response has specific status code.
     *
     * @param string $code status code
     *
     * @Then /^(?:the )?response code should be (\d+)$/
     */
    public function theResponseCodeShouldBe($code)
    {
        Assert::assertSame((int) $code, $this->browser->getLastResponse()->getStatusCode());
    }

    /**
     * Checks that response body contains specific text.
     *
     * @param string $text
     *
     * @Then /^(?:the )?response should contain "([^"]*)"$/
     */
    public function theResponseShouldContain($text)
    {
        Assert::assertRegExp('/'.preg_quote($text).'/', $this->browser->getLastResponse()->getContent());
    }

    /**
     * Checks that response body doesn't contains specific text.
     *
     * @param string $text
     *
     * @Then /^(?:the )?response should not contain "([^"]*)"$/
     */
    public function theResponseShouldNotContain($text)
    {
        Assert::assertNotRegExp('/'.preg_quote($text).'/', $this->browser->getLastResponse()->getContent());
    }

    /**
     * Checks that response body contains JSON from PyString.
     *
     * @Then /^(?:the )?response should contain json:$/
     */
    public function theResponseShouldContainJson(PyStringNode $jsonString)
    {
        $etalon = json_decode($this->replacePlaceHolder($jsonString->getRaw()), true);
        $actual = json_decode($this->browser->getLastResponse()->getContent(), true);
        if (null === $etalon) {
            throw new \RuntimeException(
                "Can not convert etalon to json:\n".$this->replacePlaceHolder($jsonString->getRaw())
            );
        }
        Assert::assertCount(count($etalon), $actual);
        foreach ($actual as $key => $needle) {
            Assert::assertArrayHasKey($key, $etalon);
            Assert::assertEquals($etalon[$key], $actual[$key]);
        }
    }

    /**
     * Prints last response body.
     *
     * @Then print response
     */
    public function printResponse()
    {
        $request = $this->browser->getLastRequest();
        $response = $this->browser->getLastResponse();
        $this->printDebug(sprintf("%s %s => %d:\n%s",
            $request->getMethod(),
            $request->getUrl(),
            $response->getStatusCode(),
            $response->getContent()
        ));
    }

    /**
     * Returns browser instance.
     *
     * @return Browser
     */
    public function getBrowser()
    {
        return $this->browser;
    }

    /**
     * Sets place holder for replacement.
     *
     * you can specify placeholders, which will
     * be replaced in URL, request or response body.
     *
     * @param string $key   token name
     * @param string $value replace value
     */
    public function setPlaceHolder($key, $value)
    {
        $this->placeHolders[$key] = $value;
    }

    /**
     * Replaces placeholders in provided text.
     *
     * @param string $string
     *
     * @return string
     */
    public function replacePlaceHolder($string)
    {
        foreach ($this->placeHolders as $key => $val) {
            $string = str_replace($key, $val, $string);
        }

        return $string;
    }

    /**
     * Returns headers, that will be used to send requests.
     *
     * @return array
     */
    protected function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Adds header.
     *
     * @param string $header
     */
    protected function addHeader($header)
    {
        $this->headers[] = $header;
    }

    /**
     * Removes a header identified by $headerName.
     *
     * @param string $headerName
     */
    protected function removeHeader($headerName)
    {
        foreach ($this->headers as $headerIndex => $headerValue) {
            if (0 === strpos($headerValue, $headerName)) {
                unset($this->headers[$headerIndex]);
            }
        }
    }
}
