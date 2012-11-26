<?php
/**
 *  @copyright The Royal National Theatre
 *  @author John-Paul Drawneek <jdrawneek@nationaltheatre.org.uk>
 */
namespace NT\Drupal\Testing\PHPUnit;

class DrupalUnitTestCase extends DrupalTestCase {
  function setUp() {
    parent::setUp();

    if (!defined('DRUPAL_ROOT')) {
      define('DRUPAL_ROOT', UPAL_ROOT);
    }
    require_once DRUPAL_ROOT . '/includes/bootstrap.inc';
    drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);
  }
}
