<?php
/**
 * ApiContext class
 * This file is part of Sonata Sandbox project.
 *
 * @since 19/05/2014
 */

use \Behat\CommonContexts\WebApiContext;

/**
 * @author Romain Mouillard <romain.mouillard@gmail.com>
 */
class ApiContext extends WebApiContext
{
    /**
     * @Then /^response pager should contain ([0-9]+) elements$/
     */
    public function theResponseShouldContainsNumberOfElements($count)
    {
        /** @var \Buzz\Message\Response $response */
        $response = $this->getBrowser()->getLastResponse();

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
        $response = $this->getBrowser()->getLastResponse();

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
     * @Then /^response pager first element should contain "(.*)"$/
     */
    public function theResponseFirstElementShouldContain($message)
    {
        if (empty($message)) {
            return;
        }

        /** @var \Buzz\Message\Response $response */
        $response = $this->getBrowser()->getLastResponse();

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
        $response = $this->getBrowser()->getLastResponse();

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

//        "errors":{"children":{"title":{"errors":["Cette valeur ne doit pas \u00eatre vide."]},"slug":[],"abstract":[],"rawContent":[],"cont

//        errors>
//                  <form name="">
//                    <errors/>
//                    <form name="title">
//                      <errors>
//                        <entry><![CDATA[Cette valeur ne doit pas être vide.]]></entry>
//                      </errors>
//                    </form>
    }

    /**
     * @Then /^the validation for "(.*)" should fail with "(.*)"$/
     */
    public function theFieldShouldBeInError($field, $message)
    {
        return $this->theValidationForFieldShouldFail($field, $message);
    }
}
 