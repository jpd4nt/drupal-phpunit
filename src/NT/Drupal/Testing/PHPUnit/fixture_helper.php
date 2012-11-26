<?php
/**
 * Description of fixture_helper
 *
 * @copyright The Royal National Theatre
 * @author John-Paul Drawneek <jdrawneek@nationaltheatre.org.uk>
 */
namespace NT\Drupal\Testing\PHPUnit;

class fixture_helper {
  
  protected static $instance;
  protected $object_list = array();

  protected function __construct() {}
  protected function __clone() {}

  public static function getInstance() {
    if (!isset(self::$instance)) {
      self::$instance = new fixture_helper();
    }
    return self::$instance;
  }
  
  public static function setup($fixture, $type = NULL) {
    if (!empty($fixture)) {
      try {
        $class = str_replace(' ', '', ucwords(strtolower($fixture)));
        $ob_str = 'NT\\Test\\Fixtures\\' . $type . '\\' . $class;
        $helper = fixture_helper::getInstance();
        $fixture_obj = new $ob_str;
        $helper->add_object($fixture_obj);
        return $fixture_obj->run();
      }
      catch (Exception $e) {
        print print_r($e, TRUE) . "\n\n";
        return FALSE;
      }
    }
    else {
      return FALSE;
    }
  }
  
  public static function clear($fixture = NULL) {
    $helper = fixture_helper::getInstance();
    if (isset($fixture)) {
      $fixture = str_replace(' ', '_', strtolower($fixture));
    }
    $helper->remove_object($fixture);
  }
  
  public function add_object($obj) {
    $this->object_list[] = $obj;
  }
  
  public function remove_object($fixture = NULL) {
    foreach ($this->object_list as $key => $obj) {
      if (isset($fixture)) {
        if ($obj instanceof $fixture) {
          $obj->reset();
          unset($this->object_list[$key]);
        }
      }
      else {
        $obj->reset();
        unset($this->object_list[$key]);
      }
    }
  }
}
