<?php

/**
 * This context is intended for table interactions
 * Kanas Maha
 */


use Behat\Mink\Element\DocumentElement;
use Behat\Gherkin\Node\TableNode;


class TableContext extends Behat\Behat\Context\BehatContext {


//functions about tables and datas filling
    /**
     * Allows to fill an array of data
     * @When /^I fill in the following fields:$/
     */
    public function iFillInTheFollowing(TableNode $table) {
        $hash = $table->getHash();
        foreach ($hash as $row) {
            // $row['name'], $row['email'], $row['phone']
        }
        //throw new PendingException();
    }

    /**
     *
     * @Then /^the following fields:$/
     */
    public function theFollowingFields(TableNode $table) {
        $hash = $table->getHash();
        foreach ($hash as $row) {
            // $row['name'], $row['email'], $row['phone']
        }
    }

    /**
     * cheks the existance of users
     * @Given /^a site have users:$/
     */
    public function aSiteHaveUsers(TableNode $table) {
        $hash = $table->getHash();
        foreach ($hash as $row) {
            // $row['name'], $row['email'], $row['phone']
        }
    }

    /**
     * Allows to have an array with empty data
     * @Given /^the following fields empty:$/
     */
    public function theFollowingFieldsEmpty(TableNode $table) {
        $hash = $table->getHash();
        foreach ($hash as $row) {
            $row['name'] = '';
            $row['email'] = '';
            $row['phone'] = '';
        }
    }
    }
?>
