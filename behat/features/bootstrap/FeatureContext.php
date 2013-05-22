<?php # features/bootstrap/FeatureContext.php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;


use Behat\Mink\Exception\UnsupportedDriverActionException,
    Behat\Mink\Exception\ExpectationException;

//use PHPUnit_Framework_ExpectationFailedException as AssertException;
//
//require_once 'PHPUnit/Autoload.php';
//require_once 'PHPUnit/Framework/Assert/Functions.php';



class FeatureContext extends Behat\Behat\Context\BehatContext
{

         /**
	 * Context initialization
	 *
	 * @param array $parameters context parameters (set them up through behat.yml)
	 */
	public function __construct(array $parameters = array())
	{
		$this->useContext('browser', new \BrowserContext($parameters));
                $this->useContext('table', new \TableContext($parameters));
                $this->useContext('user', new \UserContext($parameters));
	}

    /**
	 * Array for storing custom parameters during steps
	 *
	 * @var array
	 */
	private $parameters = array();



	/**
	 * @param string $name
	 * @return string
	 */
	public function getParameter($name)
	{
		return $this->parameters[$name];
	}

	/**
	 * @param string $name
	 * @return boolean
	 */
	public function hasParameter($name)
	{
		return isset($this->parameters[$name]);
	}

	/**
	 * @param string $name
	 * @param string $value
	 * @return void
	 */
	public function setParameter($name, $value)
	{
		$this->parameters[$name] = $value;
	}






}

?>
