<?php
/**
 *  @copyright The Royal National Theatre
 *  @author John-Paul Drawneek <jdrawneek@nationaltheatre.org.uk>
 */
namespace NT\Drupal\Testing\PHPUnit;

abstract class DrupalUnitTestCase extends DrupalTestCase {
  /** @var int Current drupal run time level */
  protected $drupal_level;
    
  function setUp($bootstrap = DRUPAL_BOOTSTRAP_FULL, $file = UPAL_USE_DB) {
    parent::setUp();
    $this->drupal_level = $bootstrap;
    DrupalTestCase::import_database(DB_DB, $file);
    global $databases;
    
    $databases = [
      'default' => [
        'default' => [
          'database' => DB_DB,
          'username' => DB_USER,
          'password' => DB_PWD,
          'host' => 'localhost',
          'port' => '',
          'driver' => 'mysql',
          'prefix' => '',
        ],
      ],
    ];
    // Finding modules is all relative, not like we have absolute paths set.
    chdir(DRUPAL_ROOT);
    drupal_bootstrap($bootstrap);
  }
  
  protected function tearDown() {
    parent::tearDown();
    DrupalTestCase::drop_tables(DB_DB);
    switch($this->drupal_level) {
      case DRUPAL_BOOTSTRAP_FULL:
      case DRUPAL_BOOTSTRAP_LANGUAGE:
      case DRUPAL_BOOTSTRAP_PAGE_HEADER:
      case DRUPAL_BOOTSTRAP_SESSION:
      case DRUPAL_BOOTSTRAP_VARIABLES:
        $this->resetAll();
      default:
        drupal_static_reset();
    }
    restore_error_handler();
    restore_exception_handler();
    spl_autoload_unregister('drupal_autoload_class');
    spl_autoload_unregister('drupal_autoload_interface');
  }
}
