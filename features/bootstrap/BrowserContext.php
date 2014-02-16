<?php

use Behat\Mink\Exception\ExpectationException;
use Behat\MinkExtension\Context\MinkContext;

/**
 * This context is intended for Browser interactions
 */
class BrowserContext extends MinkContext
{
    /**
     * @When /^I am connected with "([^"]*)" and "([^"]*)" on "([^"]*)" I should see "([^"]*)"$/
     *
     * @param string $login
     * @param string $rawPassword
     * @param string $url
     * @param string $match
     */
    public function iAmConnectedWithAndOnIShouldSee($login, $rawPassword, $url, $match)
    {
        $this->iAmConnectedWithOn($login, $rawPassword, $url);
        $this->assertPageContainsText($match);
    }

    /**
     * First, force logout, then go to the login page, fill the informations and finally go to requested page
     *
     * @Given /^I am connected with "([^"]*)" and "([^"]*)" on "([^"]*)"$/
     *
     * @param string $login
     * @param string $rawPassword
     * @param string $url
     */
    public function iAmConnectedWithOn($login, $rawPassword, $url)
    {
        $this->visit('logout');
        $this->visit('login');
        $this->fillField('_username', $login);
        $this->fillField('_password', $rawPassword);
        $this->pressButton('Login');

        $this->visit($url);
    }

    /**
     * @Given /^I follow link "([^"]*)" with class "([^"]*)"$/
     *
     * @param string $text
     * @param string $class
     */
    public function iFollowLinkWithClass($text, $class)
    {
        $link = $this->getSession()->getPage()->find(
            'xpath', sprintf("//*[@class='%s' and text() = '%s']", $class, $text)
        );

        if (!$link) {
            throw new ExpectationException(sprintf('Unable to follow the link with class: %s and text: %s', $class, $text), $this->getSession());
        }

        $link->click();
    }

    /**
     * @Given /^I follow the first link of class "([^"]*)"$/
     *
     * @param string $class
     */
    public function iFollowFirstLinkOfClass($class)
    {
        $link = $this->getSession()->getPage()->find(
            'xpath', sprintf("//*[@class='%s']", $class)
        );

        if (!$link) {
            throw new ExpectationException(sprintf('Unable to follow the link with class: %s ', $class), $this->getSession());
        }

        $link->click();
    }

    /**
     * Follow the first link found nested in a section selected with "class"
     *
     * @Given /^I follow the first link of section "([^"]*)"$/
     *
     * @param string $class
     */
    public function iFollowTheFirstLinkOfSection($class)
    {
        $link = $this->getSession()->getPage()->find(
            'xpath', sprintf("//*[@class='%s']/a", $class)
        );

        if (!$link) {
            throw new ExpectationException(sprintf('Unable to follow the nested link with class: %s', $class), $this->getSession());
        }

        $link->click();
    }

    /**
     * Follow the first link found in the first li element found nested in a section selected with "class"
     *
     * @Given /^I follow the first listed link of section "([^"]*)"$/
     *
     * @param string $class
     */
    public function iFollowTheFirstListedLinkOfSection($class)
    {
        $link = $this->getSession()->getPage()->find(
            'xpath', sprintf("//*[@class='%s']/ul/li[1]/a", $class)
        );

        if (!$link) {
            throw new ExpectationException(sprintf('Unable to follow the first listed link nested with class: %s', $class), $this->getSession());
        }

        $link->click();
    }

    /**
     * Follow the last link found in the table element found nested in a section selected with "class"
     *
     * @Given /^I follow the last listed link of section "([^"]*)"$/
     *
     * @param string $class
     */
    public function iFollowTheLastListedLinkOfSection($class)
    {
        $link = $this->getSession()->getPage()->find(
            'xpath', sprintf("//*[@class='%s']/tr[last()]/td/a", $class)
        );

        if (!$link) {
            throw new ExpectationException(sprintf('Unable to follow the last listed link nested with class: %s', $class), $this->getSession());
        }

        $link->click();
    }
}
