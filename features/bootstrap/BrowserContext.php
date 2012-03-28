<?php


use Behat\Mink\Behat\Context\MinkContext;
use Behat\Behat\Context\Step;
use Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Driver\GoutteDriver;
use Behat\Mink\Driver\SahiDriver;
use Behat\Mink\Element\DocumentElement;
use PHPUnit_Framework_ExpectationFailedException as AssertException;
use Behat\Mink\Session;

/**
 * This context is intended for Browser interractions
 */
class BrowserContext extends MinkContext
{

    /**
     * @When /^I am connected with "([^"]*)" and "([^"]*)" on "([^"]*)" I should see "([^"]*)"$/
     *
     * @param string $login
     * @param string $pwd
     * @param string $url
     * @param string $match
     */
    public function iAmConnectedWithAndOnIShouldSee($login, $pwd, $url, $match)
    {
        $this->iAmConnectedWithOn($login, $pwd, $url);
        $this->assertPageContainsText($match);
    }


    /**
     * @Given /^I am connected with "([^"]*)" and "([^"]*)" on "([^"]*)"$/
     *
     * @param string $login
     * @param string $pwd
     * @param string $url
     */
    public function iAmConnectedWithOn($login, $pwd, $url)
    {
        $this->visit($url);
        $this->fillField('_username', $login);
        $this->fillField('_password', $pwd);
        $this->pressButton('_submit');
        $this->visit($url);
    }
}