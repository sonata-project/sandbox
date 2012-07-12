<?php


use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

use Behat\MinkExtension\Context\MinkContext;

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
