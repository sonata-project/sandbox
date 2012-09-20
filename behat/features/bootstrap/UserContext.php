<?php

use Behat\MinkExtension\Context\MinkContext;
use Behat\Mink\Session;


/**
 * This context is intended for User interractions
 */
class UserContext extends Behat\Behat\Context\BehatContext {

//functions about connections/authentication
    /**
     * @Given /^I am connected with "([^"]*)" and "([^"]*)" on "([^"]*)"$/
     */
    public function iAmConnectedWithOn($login, $pwd, $url) {
        $this->getSession()->visit($url);
        //$this->fillField('_username', $login);
        //$this->fillField('_password', $pwd);
        //$this->pressButton('Me connecter');
        $this->fillField('ekino_tg7_form_retrieve_email', $login);
        $this->fillField('ekino_tg7_form_retrieve_wecabReference', $pwd);
        $this->pressButton('OK');
        $this->getSession()->visit($url);
    }

    /**
     * checks for a given username and password If we're logged in
     * @Given /^I am authenticated$/
     */
    public function iAmAuthenticated() {
        $page = $this->getSession()->getPage();
        $username_field = $page->findField('username');
        $password_field = $page->findField('password');
        $button_submit = $page->findButton('');
        $username_field->setValue('admin_login');
        $password_field->setValue('admin_password');
        $button_submit->click();
        $this->getSession();
    }
}

?>
