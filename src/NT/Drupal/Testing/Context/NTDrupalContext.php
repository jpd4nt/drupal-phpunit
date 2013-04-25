<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;
    
use Behat\MinkExtension\Context\MinkContext;
use Drupal\DrupalExtension\Context\DrupalContext;

//
// Require 3rd-party libraries here:
//
//   require_once 'PHPUnit/Autoload.php';
//   require_once 'PHPUnit/Framework/Assert/Functions.php';
//

/**
 * Features context.
 */
class NTDrupalContext extends DrupalContext
{
    
  protected $prefix;

    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @param   array   $parameters     context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
      // Initialize your context here
      // die(var_dump($parameters));
      if (!defined('UPAL_ROOT')) {
        define('UPAL_ROOT', $parameters['UPAL_ROOT']);
      }
      if (!defined('UPAL_WEB_URL')) {
        define('UPAL_WEB_URL', $parameters['UPAL_WEB_URL']);
      }
      if (!defined('PREFIX')) {
        define('PREFIX', $parameters['PREFIX']);
      }
      if (!defined('UPAL_USE_DB')) {
        define('UPAL_USE_DB', $parameters['UPAL_USE_DB']);
      }
      if (!defined('UNISH_DRUSH')) {
        if (isset($parameters['UNISH_DRUSH'])) {
          $unish_drush = $parameters['UNISH_DRUSH'];
        }
        else {
            $unish_drush = trim(`which drush`);
        }
        define('UNISH_DRUSH', $unish_drush);
      }
      if (!defined('UPAL_DB_PATH')) {
        define('UPAL_DB_PATH', $parameters['UPAL_DB_PATH']);
      }
    }
    
    protected function refreshVariables() {
      global $conf;
      cache_clear_all('variables', 'cache_bootstrap');
      $conf = variable_initialize();
    }
    protected function checkPermissions(array $permissions, $reset = FALSE) {
      $available = &drupal_static(__FUNCTION__);

      if (!isset($available) || $reset) {
        $available = array_keys(module_invoke_all('permission'));
      }

      $valid = TRUE;
      foreach ($permissions as $permission) {
        if (!in_array($permission, $available)) {
          $this->fail(t('Invalid permission %permission.', array('%permission' => $permission)), t('Role'));
          $valid = FALSE;
        }
      }
      return $valid;
    }
    /**
     * @BeforeScenario
     * @param Behat\Behat\Event\ScenarioEvent $event 
     */
    public function before($event) {
      if ($event->isSkipped()) {
        return 0;
      }
      
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
      
      //die(var_dump($event->getScenario()));
      if (!defined('DRUPAL_ROOT')) {
        define('DRUPAL_ROOT', UPAL_ROOT);
      }
      $site = DRUPAL_ROOT . '/sites/upal';

      // Restore virgin files directory.
      $files_dir = "$site/files";
      if (file_exists($files_dir)) {
        exec('chmod -R 777 ' . escapeshellarg($site), $output, $return);
        exec('rm -rf ' . escapeshellarg($site), $output, $return);
      }
//      $this->prefix = 'ntweb_1342019669_';
      $this->prefix = PREFIX . '_' . time();
      if(UPAL_USE_DB) {
          if(!file_exists($site)) {
              mkdir($site);
          }
          if(!file_exists($files_dir)) {
              mkdir($files_dir);
              mkdir($files_dir. '/styles');
              mkdir($files_dir. '/styles/thumbnail');
              mkdir($files_dir. '/styles/thumbnail/public');
              mkdir($files_dir. '/ctools');
              mkdir($files_dir. '/ctools/css');
          }
          if(!file_exists($site . '/settings.php')) {
            $settings = file_get_contents(UPAL_DB_PATH . '/settings.php');
            $settings = sprintf($settings, $this->prefix);
            file_put_contents($site . '/settings.php', $settings);
            unset($settings);
          }

          $sql_tmpl = fopen(UPAL_DB_PATH . DIRECTORY_SEPARATOR . UPAL_USE_DB, "r");
          $sql_tmpl_fix  = fopen(UPAL_DB_PATH . DIRECTORY_SEPARATOR . $this->prefix .'.sql', 'w');
          while(($buffer = fgets($sql_tmpl)) !== FALSE) {
            $buffer = str_replace('${prefix}', $this->prefix, $buffer);
            fwrite($sql_tmpl_fix, $buffer);
          }
          fclose($sql_tmpl);
          fclose($sql_tmpl_fix);
          $cmd = sprintf(
              '`%s sql-connect --uri=%s --root=%s` < %s', 
              UNISH_DRUSH,
              UPAL_WEB_URL, 
              UPAL_ROOT,
              UPAL_DB_PATH . DIRECTORY_SEPARATOR . $this->prefix .'.sql'
          );
          $time = time();
          exec($cmd, $output, $return);
          print 'Import finished took:' . (time() - $time) . "sec\n\n";
      } else {
          // Assure that we start with an empty database. Will create one if needed.
          $cmd = sprintf(
              '%s site-install --uri=%s --db-url=%s --sites-subdir=upal --root=%s --account-pass=test1234 -y minimal',
              UNISH_DRUSH,
              UPAL_WEB_URL,
              UPAL_DB_URL, 
              UPAL_ROOT
          );
          exec($cmd, $output, $return); 
      }    
      $files_dir = "$site/files";
      if (file_exists($files_dir)) {
        exec('chmod -R 777 ' . escapeshellarg($files_dir), $output, $return);
      }

      require_once DRUPAL_ROOT . '/includes/bootstrap.inc';
      chdir(DRUPAL_ROOT);
      drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);
      // Reset all static variables.
      drupal_static_reset();
      // Reset cached schema for new database prefix. This must be done before
      // drupal_flush_all_caches() so rebuilds can make use of the schema of
      // modules enabled on the cURL side.
      $connection_info = Database::getConnectionInfo('default');
      Database::removeConnection('default');
      $connection_info['default']['prefix'] = $this->prefix . '_';
      Database::addConnectionInfo('default', 'default', $connection_info['default']);
      drupal_get_schema(NULL, TRUE);
      // Reset the list of enabled modules.
      module_list(TRUE);
      // Reload global $conf array and permissions.
      $this->refreshVariables();
      $this->checkPermissions(array(), TRUE);

      // Enable modules for this test.
      $modules = func_get_args();
      if (isset($modules[0]) && is_array($modules[0])) {
        $modules = $modules[0];
      }
      if ($modules) {
        module_enable($modules, TRUE);
      }
      variable_set('file_public_path', 'sites/upal/files');
    }
    /**
     * @AfterScenario
     * @param Behat\Behat\Event\ScenarioEvent $event 
     */
    public function after($event) {
      $time = time();
      $sql = sprintf("SELECT table_name FROM information_schema.tables WHERE table_name LIKE '%s_%%'", $this->prefix);
      $result = db_query($sql);
      foreach ($result as $row) {
        db_query(sprintf("DROP TABLE %s", $row->table_name));
      }
      print 'Drop took:' . (time() - $time) . "sec\n\n";
      unlink(UPAL_DB_PATH . DIRECTORY_SEPARATOR . $this->prefix .'.sql');
      fixture_helper::clear();
    }
    /**
     * @Given /^"(?P<fixture>[^"]*)" fixture is loaded$/
     */
    public function fixtureIsLoaded($fixture)
    {
      fixture_helper::setup($fixture);
    }
    /**
     * @Given /^fixture "(?P<fixture>[^"]*)" is loaded of type "(?P<type>[^"]*)"$/
     */
    public function fixtureIsLoadedType($fixture, $type)
    {
      fixture_helper::setup($fixture, $type);
    }
}
 
