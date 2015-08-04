<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Sonata Project <https://github.com/sonata-project/SonataClassificationBundle/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Behat\Behat\Context\BehatContext;
use Behat\CommonContexts\WebApiContext;
use Behat\Gherkin\Node\TableNode;

/**
 * Behat context dedicated to test Sonata API
 *
 * @author Romain Mouillard <romain.mouillard@gmail.com>
 */
class FeatureContext extends BehatContext
{
    /**
     * @var array
     */
    private $identifiers = array();

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var string
     */
    private $filesPath;

    /**
     * {@inheritdoc}
     */
    public function __construct(array $parameters)
    {
        $this->baseUrl   = $parameters['base_url'];
        $this->filesPath = $parameters['files_path'];

        $this->useContext('api', new WebApiContext($this->baseUrl));
    }

    /**
     * @BeforeScenario
     */
    public static function setupFeature($event)
    {
        include_once realpath(__DIR__.'/../../CiHelper.php');

        CiHelper::run($event);
    }

    /**
     * Sends HTTP request to specific URL using latest identifier.
     *
     * @param string $method request method
     * @param string $url    relative url
     *
     * @When /^(?:I )?send a ([A-Z]+) request to "([^"]+)" using (?:last )?identifier:?$/
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
     * @When /^(?:I )?send a ([A-Z]+) request to "([^"]+)" using (?:last )?identifier with values:$/
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

        /** @var \Buzz\Message\Request $request */
        $request = $this->getSubcontext('api')->getBrowser()->getLastRequest();
        $headers = $request->getHeaders();
        $url = str_replace('//api', '/api', $url);

        $this->getSubcontext('api')->getBrowser()->submit($url, $fields, $method, $headers);
    }

    /**
     * @Then /^store the ([jJ][sS][oO][nN]|[xX][mM][lL]) response identifier as "(.*)"$/
     */
    public function storeTheResponseIdentifier($objectType, $alias)
    {

        $responseContent = $this->getSubcontext('api')->getBrowser()->getLastResponse()->getContent();

        $objectType = strtolower($objectType);

        switch($objectType) {
            case 'xml':
                $data = simplexml_load_string($responseContent);

                if (false === $data) {
                    throw new Exception(sprintf('Response was not XML : "%s"', $responseContent));
                }

                $identifier = $data->attributes()->id;

                if (null === $identifier) {
                    $data = current($data);
                    $identifier = $data->attributes()->id;
                }

                $this->identifiers[$alias] = (string)$identifier;
                break;
            case 'json':
                $data = json_decode($responseContent);

                if (false === $data) {
                    throw new Exception(sprintf('Response was not json : "%s"', $responseContent));
                }

                $this->identifiers[$alias] = $data->id;

                break;
        }
    }

    /**
     * Post and Put an basketelement return a basket. This method store the identifier of the first basketelement
     * of the basket.
     *
     * @Then /^store the ([jJ][sS][oO][nN]|[xX][mM][lL]) response basketelement identifier as "(.*)"$/
     */
    public function storeTheResponseBasketelementIdentifier($objectType, $alias)
    {
        $responseContent = $this->getSubcontext('api')->getBrowser()->getLastResponse()->getContent();

        $objectType = strtolower($objectType);

        switch($objectType) {
            case 'xml':
                $data = simplexml_load_string($responseContent);

                if (false === $data) {
                    throw new Exception(sprintf('Response was not XML : "%s"', $responseContent));
                }

                $element = $data->basket_elements->entry;
                $identifier = $element->attributes()->id;
                $this->identifiers[$alias] = current($identifier);

                break;
            case 'json':
                $data = json_decode($responseContent);

                if (false === $data) {
                    throw new Exception(sprintf('Response was not json : "%s"', $responseContent));
                }

                $aElements = $data->basket_elements;
                $firstElement = array_shift($aElements);
                $identifier = $firstElement->id;
                $this->identifiers[$alias] = $identifier;

                break;
        }
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

    /**
     * @Then /^response pager should contain ([0-9]+) elements$/
     */
    public function theResponseShouldContainsNumberOfElements($count)
    {
        /** @var \Buzz\Message\Response $response */
        $response = $this->getSubcontext('api')->getBrowser()->getLastResponse();

        $responseContent     = $response->getContent();
        $responseContentType = $response->getHeader('Content-Type');

        if (strstr($responseContentType, 'text/xml')) {
            $data = simplexml_load_string($responseContent);
            $found = isset($data->entries) ? count($data->entries->entry) : 0;
        } elseif (strstr($responseContentType, 'application/json')) {
            $data = json_decode($responseContent);
            $found = isset($data->entries) ? count($data->entries) : 0;
        } else {
            throw new Exception(sprintf('The response content should be json or xml to count number or elements'));
        }

        if ($found != $count) {
            throw new Exception(sprintf('There should be %s elements, found %s', $count, $found));
        }
    }

    /**
     * @Then /^response pager should display page ([0-9]+) with ([0-9]+) elements$/
     */
    public function theResponseShouldDisplayExpectedPageAndElementsCount($page, $perPage)
    {
        /** @var \Buzz\Message\Response $response */
        $response = $this->getSubcontext('api')->getBrowser()->getLastResponse();

        $responseContent     = $response->getContent();
        $responseContentType = $response->getHeader('Content-Type');

        if (strstr($responseContentType, 'text/xml')) {
            $data = simplexml_load_string($responseContent);
        } elseif (strstr($responseContentType, 'application/json')) {
            $data = json_decode($responseContent);
        } else {
            throw new Exception(sprintf('The response content should be json or xml to count number or elements'));
        }

        $responsePerPage = isset($data->per_page) ? $data->per_page : 0;
        $responsePage    = isset($data->page) ? $data->page : 0;

        if ($responsePage != $page) {
            throw new Exception(sprintf('The response should display page %s, page %s displayed', $page, $responsePage));
        }
        if ($responsePerPage != $perPage) {
            throw new Exception(sprintf('The response should display %s elements per page, %s displayed', $perPage, $responsePerPage));
        }
    }

    /**
     * @Then /^response pager data should be consistent$/
     */
    public function theResponsePagerDataShouldBeConsistent()
    {
        /** @var \Buzz\Message\Response $response */
        $response = $this->getSubcontext('api')->getBrowser()->getLastResponse();

        $responseContent     = $response->getContent();
        $responseContentType = $response->getHeader('Content-Type');

        if (strstr($responseContentType, 'text/xml')) {
            $data = simplexml_load_string($responseContent);
            $count = isset($data->entries) ? count($data->entries->entry) : 0;
        } elseif (strstr($responseContentType, 'application/json')) {
            $data = json_decode($responseContent);
            $count = isset($data->entries) ? count($data->entries) : 0;
        } else {
            throw new Exception(sprintf('The response content should be json or xml to count number or elements'));
        }

        $perPage      = isset($data->per_page) ? (int)$data->per_page : 0;
        $totalElement = isset($data->total) ? (int)$data->total : 0;
        $lastPage     = isset($data->last_page) ? (int)$data->last_page : 0;
        $currentPage  = isset($data->page) ? (int)$data->page : 0;

        if ($totalElement > 0 && $lastPage != ceil($totalElement / $perPage)) {
            throw new Exception(sprintf('The pager per_page value is inconsistent'));
        }

        if ($totalElement < ($currentPage * $perPage)) {
            $expectedCount = $totalElement - (($currentPage-1) * $perPage);

            if ($currentPage != $lastPage) {
                throw new Exception(sprintf('The pager last page is inconsistent. Current page %s seems to be the last, but last page is %s', $currentPage, $lastPage));
            }
        } else {
            $expectedCount = $perPage;
        }

        if ($count != $expectedCount) {
            throw new Exception(sprintf('The number of results provided by the pager is inconsistent. Got %s results, expected %s', $count, $expectedCount));
        }
    }

    /**
     * @Then /^response pager first element should contain "(.*)"$/
     */
    public function theResponseFirstElementShouldContain($message)
    {
        if (empty($message)) {
            return;
        }

        /** @var \Buzz\Message\Response $response */
        $response = $this->getSubcontext('api')->getBrowser()->getLastResponse();

        $responseContent     = $response->getContent();
        $responseContentType = $response->getHeader('Content-Type');

        if (strstr($responseContentType, 'text/xml')) {
            $data         = simplexml_load_string($responseContent);
            $firstElement = isset($data->entries) ? $data->entries->entry[0] : array();
        } elseif (strstr($responseContentType, 'application/json')) {
            $data         = json_decode($responseContent);
            $firstElement = isset($data->entries) ? $data->entries[0] : array();
        } else {
            throw new Exception(sprintf('The response content should be json or xml to count number or elements'));
        }

        $found = false;
        foreach ($firstElement as $value) {
            if (strpos($value, $message) !== false) {
                $found = true;
                break;
            }
        }

        if (!$found) {
            throw new Exception(sprintf('The response first element does not contain "%s"', $message));
        }
    }

    /**
     * @Then /^response should contain ([0-9]+) validation errors$/
     */

    /**
     * @Then /^the validation for "(.*)" should fail$/
     */
    public function theValidationForFieldShouldFail($field, $message = null)
    {
        /** @var \Buzz\Message\Response $response */
        $response = $this->getSubcontext('api')->getBrowser()->getLastResponse();

        $responseContent     = $response->getContent();
        $responseContentType = $response->getHeader('Content-Type');

        $inError  = false;
        $messages = array();

        if (strstr($responseContentType, 'text/xml')) {
            $data = simplexml_load_string($responseContent);
            if (is_object($data->errors->form)) {
                /** @var SimpleXMLElement $child */
                foreach ($data->errors->form->children() as $child) {
                    if ($child->attributes() == $field) {
                        if ($child->count() > 0) {
                            $inError = true;
                            /** @var SimpleXMLElement $error */
                            foreach ($child->errors->children() as $error) {
                                $messages[] = $error;
                            }
                        }
                    }
                }
            }
        } elseif (strstr($responseContentType, 'application/json')) {
            $data   = json_decode($responseContent);
            $errors = isset($data->errors) ? $data->errors->children : array();
            if (isset($errors->$field) && isset($errors->$field->errors)) {
                $inError  = true;
                $messages = $errors->$field->errors;
            }
        } else {
            throw new Exception('The response content should be json or xml to search for validations errors');
        }

        if (!$inError) {
            throw new Exception(sprintf('Field %s should have a validation error, some errors were raised but found none about this field.', $field));
        }

        if (!is_null($message) && (sizeof($messages) == 0 || !in_array($message, $messages))) {
            throw new Exception(sprintf('Field %s should have "%s" validation error, but only got following errors: %s.', $field, $message, implode(PHP_EOL, $messages)));
        }
    }

    /**
     * @Then /^the validation for "(.*)" should fail with "(.*)"$/
     */
    public function theFieldShouldBeInError($field, $message)
    {
        $this->theValidationForFieldShouldFail($field, $message);
    }

    /**
     * @Given /^I have a Post identified by "(.*)" with values:$/
     */
    public function iHaveAPostIdentifiedByWithValues($identifier, TableNode $values)
    {
        return $this->iHaveAPostIdentifiedBy($identifier, $values);
    }

    /**
     * @Given /^I have a Post identified by "(.*)"$/
     */
    public function iHaveAPostIdentifiedBy($identifier, TableNode $values = null)
    {
        if (is_null($values)) {
            $values = new TableNode(<<<TABLE
      | title                 | My post title       |
      | slug                  | my-post-slug        |
      | abstract              | My abstract content |
      | rawContent            | My raw content      |
      | contentFormatter      | markdown            |
      | enabled               | 1                   |
      | commentsEnabled       | 1                   |
      | commentsDefaultStatus | 1                   |
      | author                | 1                   |
TABLE
            );
        }

        return array(
            new \Behat\Behat\Context\Step\When('I send a POST request to "/api/news/posts.xml" with values:', $values),
            new \Behat\Behat\Context\Step\Then('the response code should be 200'),
            new \Behat\Behat\Context\Step\Then('response should contain "xml" object'),
            new \Behat\Behat\Context\Step\Then('response should contain "created_at"'),
            new \Behat\Behat\Context\Step\Then(sprintf('store the XML response identifier as "%s"', $identifier)),
        );
    }

    /**
     * @Given /^I have a Comment identified by "(.*)" on Post "(.*)" with values:$/
     */
    public function iHaveACommentIdentifiedByWithValues($identifier, $postIdentifier, TableNode $values)
    {
        return $this->iHaveCommentIdentifiedBy($identifier, $postIdentifier, $values);
    }

    /**
     * @Given /^I have a Comment identified by "(.*)" on Post "(.*)"$/
     */
    public function iHaveCommentIdentifiedBy($identifier, $postIdentifier, TableNode $values = null)
    {
        if (is_null($values)) {
            $values = new TableNode(<<<TABLE
      | name    | New comment name       |
      | email   | new@email.org          |
      | url     | http://www.new-url.com |
      | message | My new comment message |
      | status  | 1                      |
TABLE
            );
        }

        /** @var FeatureContext $mainContext */
        if (!isset($this->identifiers[$postIdentifier])) {
            throw new Exception(sprintf('There is no post identified by "%s"', $postIdentifier));
        }

        $postId = $this->identifiers[$postIdentifier];

        return array(
            new \Behat\Behat\Context\Step\When(sprintf('I send a POST request to "/api/news/posts/%d/comments.xml" with values:', $postId), $values),
            new \Behat\Behat\Context\Step\Then('the response code should be 200'),
            new \Behat\Behat\Context\Step\Then('response should contain "xml" object'),
            new \Behat\Behat\Context\Step\Then('response should contain "created_at"'),
            new \Behat\Behat\Context\Step\Then(sprintf('store the XML response identifier as "%s"', $identifier)),
        );
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

        if (false === strpos($response->getHeader('Content-Disposition'), 'attachment')) {
            throw new Exception(sprintf('Response Content-Disposition header not attachment: "%s"', $response->getHeader('Content-Disposition')));
        }
    }

    /**
     * Send a request which body is the binary given by path
     *
     * @When /^(?:I )?send a ([A-Z]+) request to "([^"]*)" with the binary "([^"]*)"$/
     */
    public function iSendARequestWithTheBinary($method, $url, $path)
    {
        if ($this->filesPath) {
            $fullPath = rtrim(realpath($this->filesPath), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$path;
            if (is_file($fullPath)) {
                $path = $fullPath;
            }
        }

        if (($content = file_get_contents($path)) === false) {
            throw new \Exception(sprintf('Unable to get the content of the binary %s', $path));
        }

        $headers = array(
            'Content-type'   => 'application/octet-stream',
            'Content-Length' => filesize($path),
            'Authorization'  => 'Basic YWRtaW46YWRtaW4=',
        );

        $this->sendRequestWithIdentifiers($method, $url, $headers, $content);
    }

    /**
     * Send an http request after replacing url parameters by actual values
     *
     * @When /^(?:I )?send a ([A-Z]+) request containing identifier to "([^"]*)"$/
     */
    public function iSendAGetRequestContainingIdentifierTo($method, $url)
    {
        $headers = array(
            'Authorization' => 'Basic YWRtaW46YWRtaW4=',
        );

        $this->sendRequestWithIdentifiers($method, $url, $headers);
    }

    /**
     * @param string $method
     * @param string $url
     * @param array  $headers
     * @param string $content
     *
     * @throws Exception
     */
    protected function sendRequestWithIdentifiers($method, $url, $headers = array(), $content = '')
    {
        if (!in_array($method, array('GET', 'PUT', 'POST', 'DELETE', 'PATCH'))) {
            throw new \Exception(sprintf('Undefined method %s', $method));
        }

        $url = $this->baseUrl.$this->replaceIdentifiers($url);
        $url = str_replace('//api', '/api', $url);

        $this->getSubcontext('api')->getBrowser()->{strtolower($method)}($url, $headers, $content);
    }
}

