<?php
/**
 *  @copyright The Royal National Theatre
 *  @author John-Paul Drawneek <jdrawneek@nationaltheatre.org.uk>
 */
namespace NT\Drupal\Testing\Fixtures;

abstract class fixtures {
  
  public function __construct() {
    
  }
  /**
   * Get the current data for this fixture.
   *
   * @return array
   *   Data
   */
  public function getData() {
    return $this::$data;
  }
  /**
   * Reset this fixture to have nid of NULL.
   */
  public function reset() {
    $this::$nid = NULL;
  }
  /**
   * Main function to create a fixture.
   *
   * @return Int
   *   NID of the fixture created.
   */
  public function run() {
    if (!isset($this::$nid)) {
      $this->dependanices($this::$data);
      $this::$nid = $this->install_node($this::$data);
    }
    return $this::$nid;
  }
  
  protected function dependanices(&$node) {
    foreach ($node as $key => &$value) {
      if ($key === 'nid' && is_object($value)) {
        $value = $this->install_node($value);
      }
      elseif ($key === 'nid' && !is_numeric($value) && is_string($value)) {
        $value = \NT\Drupal\Testing\PHPUnit\fixture_helper::setup($value, 'Image');
      }
      elseif ($key === 'nid' && !is_numeric($value) && is_array($value)) {
        $value = \NT\Drupal\Testing\PHPUnit\fixture_helper::setup($value['source'], $value['type']);
      }
      else {
        if (is_array($value)) {
          $this->dependanices($value);
        }
      }
    }
  }
}
