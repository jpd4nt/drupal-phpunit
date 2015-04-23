<?php
/**
 *  @copyright The Royal National Theatre
 *  @author John-Paul Drawneek <jdrawneek@nationaltheatre.org.uk>
 */
namespace NT\Drupal\Testing\PHPUnit;

class upal {
  
  /*
   * Initialize our environment at the start of each run (i.e. suite).
   */
  public static function init() {
    // UNISH_DRUSH value can come from phpunit.xml or `which drush`.
      if (!defined('UNISH_DRUSH')) {
          // Let the UNISH_DRUSH environment variable override if set.
          $unish_drush = isset($_SERVER['UNISH_DRUSH']) ? $_SERVER['UNISH_DRUSH'] : NULL;
          $unish_drush = isset($GLOBALS['UNISH_DRUSH']) ? $GLOBALS['UNISH_DRUSH'] : $unish_drush;
          if (empty($unish_drush)) {
          // $unish_drush = Drush_TestCase::is_windows() ? exec('for %i in (drush) do @echo.   %~$PATH:i') : trim(`which drush`);
              $unish_drush = trim(`which drush`);
          }
          define('UNISH_DRUSH', $unish_drush);
      }

      if(!defined('UPAL_USE_DB')) {
          define('UPAL_USE_DB', FALSE);
      }

      if(!defined('PREFIX')) {
          define('PREFIX', '');
      }

    // We read from globals here because env can be empty and ini did not work in quick test.
      if (!defined('UPAL_DB_URL')) {
          define('UPAL_DB_URL', getenv('UPAL_DB_URL') ? getenv('UPAL_DB_URL') : (!empty($GLOBALS['UPAL_DB_URL']) ? $GLOBALS['UPAL_DB_URL'] : 'mysql://root:@127.0.0.1/upal'));
      } 

    // Make sure we use the right Drupal codebase.
      if (!defined('UPAL_ROOT')) {
          define('UPAL_ROOT', getenv('UPAL_ROOT') ? getenv('UPAL_ROOT') : (isset($GLOBALS['UPAL_ROOT']) ? $GLOBALS['UPAL_ROOT'] : realpath('.')));
      }
      //chdir(UPAL_ROOT);

    // The URL that browser based tests should use.
      if (!defined('UPAL_WEB_URL')) {
          define('UPAL_WEB_URL', getenv('UPAL_WEB_URL') ? getenv('UPAL_WEB_URL') : (isset($GLOBALS['UPAL_WEB_URL']) ? $GLOBALS['UPAL_WEB_URL'] : 'http://upal'));
      }

    define('UPAL_TMP', getenv('UPAL_TMP') ? getenv('UPAL_TMP') : (isset($GLOBALS['UPAL_TMP']) ? $GLOBALS['UPAL_TMP'] : sys_get_temp_dir()));
    // define('UNISH_SANDBOX', UNISH_TMP . '/drush-sandbox');

    // Cache dir lives outside the sandbox so that we get persistence across classes.
    $cache = UPAL_TMP . '/upal-cache';
    putenv("CACHE_PREFIX=" . $cache);

    // Set the env vars that Drupal expects. Largely copied from drush.
    $url = parse_url(UPAL_WEB_URL);

    if (array_key_exists('path', $url)) {
      $_SERVER['PHP_SELF'] = $url['path'] . '/index.php';
    }
    else {
      $_SERVER['PHP_SELF'] = '/index.php';
    }

    $_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'] = $_SERVER['PHP_SELF'];
    $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
    $_SERVER['REQUEST_METHOD']  = NULL;

    $_SERVER['SERVER_SOFTWARE'] = NULL;
    $_SERVER['HTTP_USER_AGENT'] = NULL;

    $_SERVER['HTTP_HOST'] = $url['host'];
    $_SERVER['SERVER_PORT'] = array_key_exists('port', $url) ? $url['port'] : NULL;
    
    if (!defined('DRUPAL_ROOT')) {
      define('DRUPAL_ROOT', UPAL_ROOT);
    }
    require_once DRUPAL_ROOT . '/includes/bootstrap.inc';
  }
}
