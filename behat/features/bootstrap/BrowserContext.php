<?php


use Behat\Behat\Context\Step;
use Behat\Mink\Element\DocumentElement;
use Behat\Mink\Session;
use Behat\MinkExtension\Context\MinkContext;
//use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Driver\Selenium2;
//use PHPUnit_Framework_ExpectationFailedException as AssertException;
use Selenium\Client as SeleniumClient;

/**
 * This context is intended for Browser interractions
 */
class BrowserContext extends MinkContext {

    /**
     * Timeout value
     *
     * @var int
     */
    private $timeout = 10;
    
    /**
     * Date format
     *
     * @var string
     */
    private $dateFormat = 'dmYHi';

    public function getSymfonyProfile() {
        $driver = $this->getSession()->getDriver();
        if (!$driver instanceof SymfonyDriver) {
            throw new UnsupportedDriverActionException(
                    'You need to tag the scenario with ' .
                    '"@mink:symfony". Using the profiler is not ' .
                    'supported by %s', $driver
            );
        }

        $profile = $driver->getClient()->getProfile();
        if (false === $profile) {
            throw new \RuntimeException(
                    'Emails cannot be tested as the profiler is ' .
                    'disabled.'
            );
        }

        return $profile;
    }

    public function canIntercept() {
        $driver = $this->getSession()->getDriver();
        if (!$driver instanceof GoutteDriver) {
            throw new UnsupportedDriverActionException(
                    'You need to tag the scenario with ' .
                    '"@mink:goutte" or "@mink:symfony". ' .
                    'Intercepting the redirections is not ' .
                    'supported by %s', $driver
            );
        }
    }



    /**
     * @Given /^I should be redirected to "([^"]*)"$/
     */
    public function iAmRedirectedTo($actualPath) {

        $client = $this->getSession()->getDriver()->getClient()->followRedirects(true);
    }

    /**
     * @Then /^I do not follow redirect$/
     */
    public function iDoNotFollowRedirect() {

        $this->getSession()->getDriver()->getClient()->followRedirects(false);
    }

    /**
     * @Then /^I should get an email on "(?P<email>[^"]+)" with:$/
     */
    public function iShouldGetAnEmail($email, PyStringNode $text) {
        $error = sprintf('No message sent to "%s"', $email);
        $profile = $this->getSymfonyProfile();
        $collector = $profile->getCollector('swiftmailer');

        foreach ($collector->getMessages() as $message) {
            // Checking the recipient email and the X-Swift-To
            // header to handle the RedirectingPlugin.
            // If the recipient is not the expected one, check
            // the next mail.
            $correctRecipient = array_key_exists(
                    $email, $message->getTo()
            );
            $headers = $message->getHeaders();
            $correctXToHeader = false;
            if ($headers->has('X-Swift-To')) {
                $correctXToHeader = array_key_exists($email, $headers->get('X-Swift-To')->getFieldBodyModel()
                );
            }

            if (!$correctRecipient && !$correctXToHeader) {
                continue;
            }

            try {
                // checking the content
                return assertContains(
                                $text->getRaw(), $message->getBody()
                );
            } catch (AssertException $e) {
                $error = sprintf(
                        'An email has been found for "%s" but without ' .
                        'the text "%s".', $email, $text->getRaw()
                );
            }
        }

        throw new ExpectationException($error, $this->getSession());
    }

   /**
     * @Given /^I wait for the total page loading$/
     */
    public function iWaitForTheTotalPageLoading()
    {
        $this->getSession()->wait(10000);
    }



    /*     * *********************************************************************************************************** */

    /**
     * Fills in form field with specified id|name|label|value using predefined param.
     *
     * @When /^(?:|I )fill in "(?P<field>(?:[^"]|\\")*)" with param "(?P<name>(?:[^"]|\\")*)"$/
     */
    public function fillFieldFromParam($field, $name) {
        $field = str_replace('\\"', '"', $field);
        $value = str_replace('\\"', '"', $this->getParameter($name));
        if ($value == '') {
            throw new PendingException($name . ' value must be defined in behat.yml');
        } else {
            $this->getSession()->getPage()->fillField($field, $value);
        }
    }

    /**
     * @Given /^I fill in "([^"]*)" with  "([^"]*)"$/
     */
    public function iFillInWith($argument1, $argument2) {
        throw new PendingException();
    }

    /**
     * @Given /^I should get "([^"]*)"$/
     */
    public function iShouldGet($argument1) {
        throw new PendingException();
    }

    /**
     * @When /^I  press "([^"]*)"$/
     */
    public function iPress($argument1) {
        $page = $this->getSession()->getPage();
        $button_submit = $page->findButton('');
        $button_submit->click();
    }

    /**
     * @Then /^I should  see "([^"]*)"$/
     */
    public function iShouldSee($argument1) {
        throw new PendingException();
    }

    /**
     * @Then /^I wait for the suggestion box to appear$/
     */
    public function iWaitForTheSuggestionBoxToAppear() {
        $this->getSession()->wait(5000, "$('.dropdown-menu').children().length > 0"
        );
    }
    /**
     * @Given /^I wait for the box to appear$/
     */
    public function iWaitForTheBoxToAppear()
    {
      $this->getSession()->wait(5000, "$('#ui-datepicker-div').show()"
        );
    }


    /**
     * @Given /^I trigger the input "([^"]*)"$/
     */
    public function iTriggerTheInput($field)
    {
        $el = $this->getSession()->getPage()->find('css', '#'.$field.'');
        $el->click();
    }


    /**
     * @Given /^I click on "([^"]*)"$/
     */
    public function iClickOn($field)
    {
        $this->clickLink($field);
        $this->getSession()->wait(2000);   // wait 2sec
    }


    /*     * *********************************************************************************************************** */
//Functions about fields/buttons which are enabled/disabled

    /**
     * Checks, that field with the attribute readonly is enabled
     *
     * @Then /^the field "(?P<myargumentlabel>[^"]*)" should be disabled$/
     */
    public function theFieldShouldBeDisabled($element) {
        $node = $this->getSession()->getPage()->findById($element);
        if ($node === null) {
            throw new \Exception(sprintf('There is no "%s" element', $element));
        }
        if (!$node->hasAttribute('readonly')) {
            throw new \Exception(sprintf('The element "%s" is not disabled', $element));
        }
    }

    /**
     * Checks, that field with A given ID and having the attribute readonly
     *
     * @Then /^the field "(?P<myargumentlabel>[^"]*)" should not be disabled$/
     */
    public function theFieldShouldNotBeDisabled($element) {
        $node = $this->getSession()->getPage()->findById($element);
        if ($node === null) {
            throw new \Exception(sprintf('There is no "%s" element', $element));
        }
        if ($node->hasAttribute('readonly')) {
            throw new \Exception(sprintf('The element "%s" is disabled', $element));
        }
    }

    /**
     * @Given /^the button "([^"]*)" should be disabled$/
     */
    public function theButtonShouldBeDisabled($element) {

        $node = $this->getSession()->getPage()->find('css', '.' . $element);

        if ($node === null) {
            throw new \Exception(sprintf('There is no "%s" element', $element));
        }
        if (!$node->hasAttribute('readonly')) {
            throw new \Exception(sprintf('The element "%s" is not disabled', $element));
        }
    }

    /**
     * @Given /^the button "([^"]*)" should be enabled$/
     */
    public function theButtonShouldBeEnabled($element) {

        $node = $this->getSession()->getPage()->find('css', '.' . $element);

        if ($node === null) {
            throw new \Exception(sprintf('There is no "%s" element', $element));
        }
        if ($node->hasAttribute('readonly')) {
            throw new \Exception(sprintf('The element "%s" is disabled', $element));
        }
    }

    /**
     * @Then /^the "([^"]*)" field should contain "([^"]*)" and should be disabled$/
     */
    public function theFieldShouldContainAndShouldBeDisabled($argument1, $argument2) {
        return array(
            new Step\Given("I am on \"/contact\""),
            new Step\Then("the \"$argument1\" field should contain \"$argument2\""),
            new Step\Then("the field \"$argument1\" should be disabled"),
        );
    }

    /**
     * @Given /^the field "([^"]*)" should contain "([^"]*)" and should not be disabled$/
     */
    public function theFieldShouldContainAndShouldNotBeDisabled($argument1, $argument2) {
        return array(
            new Step\Given("I am on \"/contact\""),
            new Step\Then("the \"$argument1\" field should contain \"$argument2\""),
            new Step\Then("the field \"$argument1\" should not be disabled"),
        );
    }

    /*     * *********************************************************************************************************** */

//Functions to count the occurences

    /**
     * @Given /^I should have "([^"]*)" "([^"]*)" elements in "([^"]*)"?$/
     */
    public function iShouldHaveNElements($occurences, $element, $where) {
        $nodes = $this->getSession()->getPage()->findAll('css', $where . '>' . $element);
        $actual = sizeof($nodes);
        if ($actual !== (int) $occurences) {
            throw new Exception(sprintf("%s occurences of %s found, %s expected", $actual, $element, $occurences));
        }
    }

    /**
     * @Given /^I should have "([^"]*)" "([^"]*)" in the element "([^"]*)" with the class "([^"]*)"?$/
     * Attends to find a specific css with a givin class in the structure
     */
    public function iShouldHaveNElem($occurences, $element, $where, $class) {
        $nodes = $this->getSession()->getPage()->findAll('css', $where . '[class="' . $class . '"]>' . $element);
        $actual = sizeof($nodes);
        if ($actual !== (int) $occurences) {
            throw new Exception(sprintf("%s occurences of %s found, %s expected", $actual, $element, $occurences));
        }
    }

    /*     * *********************************************************************************************************** */

    /**
     * Cheks for a given form with steps if the appropriate one is active
     * @Given /^the ([0-9]+)(?:st|nd|rd|th) step should be active$/
     */
    public function theFormStepShouldBeActive($arg1) {
        $menu = $this->getSession()->getPage()->Find('css', sprintf('menu>li:nth-child(%s)[class=active]', $arg1));
        switch ($i) {
            case $i = 1:
                $suff = "st";
                break;
            case $i = 2:
                $suff = 'nd';
                break;
            case $i = 3:
                $suff = 'rd';
                break;
            case $i = 4:
                $suff = 'th';
                break;
            case $i = 5:
                $suff = 'th';
                break;
        }

        if ((null === $menu)) {
            throw new \Exception(sprintf('The %s%s step was not found', $arg1, $suff));
        }
    }

    /**
     * checks if an element of a given list ul li has the css attribute active
     * @Given /^the element "([^"]*)" should be active$/
     */
    public function theElementShouldBeActive($argument1) {
        $list = $this->getSession()->getPage()->find('css', 'aside');    //return the left side list
        throw new \Exception(sprintf('%s ', $list->getHtml()));
    }

    /*     * *********************************************************************************************************** */
    //Functions about checkbox and radio buttons

    /**
     * checks that a radio button with a given id is checked
     * @Then /^the element "([^"]*)" should be checked$/
     */
    public function theElementShouldBeChecked($arg) {
        $elem = $this->getSession()->getPage()->findById($arg);
        if ($elem == null) {
            throw new \Exception(sprintf('L\élèment %s est null', $arg));
        }
        if (!$elem->hasAttribute('checked')) {
            //echo $response = $elem->getHtml();
            throw new \Exception(sprintf('the radio %s is not checked', $elem->getHtml()));
        }
    }

    /**
     * @When /^I check the radio "([^"]*)"$/
     */
    public function iCheckTheRadio($elem) {
        $radio_button = $this->getSession()->getPage()->findField($elem);

        if (null === $radio_button) {
            throw new ElementNotFoundException(
                    $this->getSession(), 'form field', 'id|name|label|value', $field
            );
        }
        $value = $radio_button->getAttribute('value');
        $this->fillField($elem, $value);
    }

    /*     * *********************************************************************************************************** */
// Functions concerning fields

    /**
     * @Then /^the field "([^"]*)" should be on error$/
     */
    public function theFieldShouldBeOnError($inputId) {
        var_dump($this->getSession()->evaluateScript("(function(){ return document.getElementById('ekino_contact_form_lastName'); })()"));
        die;
        //$elem = $this->getSession()->getPage()->Find('css',sprintf('form.step-form',$argument1));
        $elem = $this->getSession()->getPage()->Find('css', sprintf('form[class=step-form] div[class=.*error]>input[id=%s]', $inputId));
        var_dump($elem->getHtml());
        die;
        if ((null === $elem)) {
            throw new \Exception(sprintf('The %s element was not found', $inputId));
        }
    }

    /**
     * @Given /^the response should contain "([^"]*)" and the field should contain "([^"]*)"$/
     */
    public function theResponseShouldContainAndTheFieldShouldContain($argument1) {
        return array(
            new Step\Given("I am on \"/\""),
            new Step\Then("the response should contain \"$argument1\" "),
            new Step\Then("the field \"$argument1\" should contain \"\""),
        );
    }

    /*     * *********************************************************************************************************** */

//Functions to check the selection of an element in a select option tag

    /**
     * @Given /^the "([^"]*)" in "([^"]*)" should be selected$/
     */
    public function theinShouldBeSelected($optionValue, $select) {
        $selectElement = $this->getSession()->getPage()->find('named', array('select', "\"{$select}\""));
        $optionElement = $selectElement->find('named', array('option', "\"{$optionValue}\""));
        //it should have the attribute selected and it should be set to selected
        assertTrue($optionElement->hasAttribute("selected"));
        assertTrue($optionElement->getAttribute("selected") == "selected");
    }

    /**
     * @Given /^the "([^"]*)" in "([^"]*)" should not be selected$/
     */
    public function inShouldNotBeSelected($optionValue, $select) {
        $selectElement = $this->getSession()->getPage()->find('named', array('select', "\"{$select}\""));
        $optionElement = $selectElement->find('named', array('option', "\"{$optionValue}\""));
        //it should have the attribute selected and it should be set to selected
        assertFalse($optionElement->hasAttribute("selected"));
    }

    /*     * *********************************************************************************************************** */

//  Functions to check the visibility of a given element

    /**
     * @Given /^the element "([^"]*)" should be visible$/
     */
    public function theElementShouldBeVisible($argument) {
        $el = $this->getSession()->getPage()->findById($argument);
        if (!empty($el)) {
            assertTrue($el->isVisible());
        } else {
            throw new Exception("Element ({$argument}) not found");
        }
    }

    /**
     * @Given /^the element "([^"]*)" should not be visible$/
     */
    public function theElementShouldNotBeVisible($argument) {
        $el = $this->getSession()->getPage()->findById($argument);
        if (!empty($el)) {
            assertFalse($el->isVisible());
        } else {
            throw new Exception("Element ({$argument}) not found");
        }
    }

    /*     * *********************************************************************************************************** */

//functions about Json

    /**
     * @Then /^should see valid JSON$/
     */
    public function shouldSeeValidJSON() {
        $json = $this->getSession()->getPage()->getContent();
        assertTrue($json !== 0 && (false !== json_decode($json)));
    }

    /*     * *********************************************************************************************************** */
}

?>
