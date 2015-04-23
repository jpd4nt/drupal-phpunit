<?php
/**
 *  @copyright The Royal National Theatre
 *  @author John-Paul Drawneek <jdrawneek@nationaltheatre.org.uk>
 */
namespace NT\Drupal\Testing\PHPUnit;

abstract class DrupalUnitTestCase extends DrupalTestCase {
  function setUp($bootstrap = 7, $file = UPAL_USE_DB) {
    parent::setUp();
    $this->import_database(DB_DB, $file);
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

    if (!defined('DRUPAL_ROOT')) {
      define('DRUPAL_ROOT', UPAL_ROOT);
    }
    require_once DRUPAL_ROOT . '/includes/bootstrap.inc';
    drupal_bootstrap($bootstrap);
  }
  
  protected function tearDown() {
    parent::tearDown();
    $this->drop_tables(DB_DB);
  }
}
