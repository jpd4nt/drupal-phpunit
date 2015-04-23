<?php
/**
 *  @copyright The Royal National Theatre
 *  @author John-Paul Drawneek <jdrawneek@nationaltheatre.org.uk>
 */
namespace NT\Drupal\Testing\PHPUnit;

abstract class DrupalUnitTestCase extends DrupalTestCase {
  /** @var int Current drupal run time level */
  protected $drupal_level;
    
  function setUp($bootstrap = 7, $file = UPAL_USE_DB) {
    parent::setUp();
    $this->drupal_level = $bootstrap;
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

    drupal_bootstrap($bootstrap);
  }
  
  protected function tearDown() {
    parent::tearDown();
    $this->drop_tables(DB_DB);
    switch($this->drupal_level) {
      case 7:
      case 6:
      case 5:
      case 4:
      case 3:
        $this->resetAll();
      default:
        drupal_static_reset();
    }
  }
}
