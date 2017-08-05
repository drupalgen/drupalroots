<?php

use Drupal\DrupalExtension\Context\DrushContext;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Behat\Tester\Exception\PendingException;

/**
 * Defines application features from the specific context.
 */
class ConfigurationContext extends DrushContext implements SnippetAcceptingContext {

  /**
   * Initializes context.
   *
   * Every scenario gets its own context instance.
   * You can also pass arbitrary arguments to the
   * context constructor through behat.yml.
   */
  public function __construct() {
  }

  /**
   * @Then drush output should contain:
   */
  public function drushOutputShouldContain(PyStringNode $string)
  {
    if (strpos((string) $this->readDrushOutput(), $this->fixStepArgument($string)) === FALSE) {
      throw new \Exception(sprintf("The last drush command output did not contain '%s'.\nInstead, it was:\n\n%s'",
        $string, $this->drushOutput));
    }

    //throw new PendingException();
  }
}
