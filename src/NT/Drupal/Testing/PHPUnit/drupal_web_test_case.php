<?php
/**
 *  @copyright The Royal National Theatre
 *  @author John-Paul Drawneek <jdrawneek@nationaltheatre.org.uk>
 */
namespace NT\Drupal\Testing\PHPUnit;

abstract class DrupalWebTestCase extends DrupalTestCase {
  
  protected $prefix;

  public function setUp() {
    parent::setUp();

    \PHPUnit_Framework_Error_Warning::$enabled = FALSE;

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
    $this->prefix = PREFIX . '_' . time();
    if(UPAL_USE_DB) {
        if(!file_exists($site)) {
            mkdir($site);
        }
        if(!file_exists($files_dir)) {
            mkdir($files_dir);
        }

        $include_path = realpath(
          dirname(__FILE__) . '/../../../../../../../../includes/'
        );

        if(!file_exists($site . '/settings.php')) {
          $settings = file_get_contents(
            $include_path . '/settings.php'
          );
          $settings = sprintf($settings, $this->prefix);
          file_put_contents($site . '/settings.php', $settings);
          unset($settings);
        }

        // Copy modules to test env. 
        if (defined('COPY_MODULES') && COPY_MODULES) {
          symlink(
            DRUPAL_ROOT . '/sites/' . COPY_MODULES,
            DRUPAL_ROOT . '/sites/upal/modules'
          );
        }

        $sql_tmpl = fopen($include_path . DIRECTORY_SEPARATOR . UPAL_USE_DB, "r");
        $sql_tmpl_fix  = fopen($include_path . DIRECTORY_SEPARATOR . $this->prefix .'.sql', 'w');
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
            $include_path . DIRECTORY_SEPARATOR . $this->prefix .'.sql'
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
        // Debug code
//        print "$cmd\n\n";
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

    $connection_info = \Database::getConnectionInfo('default');
    \Database::removeConnection('default');
    $connection_info['default']['prefix'] = $this->prefix . '_';
    \Database::addConnectionInfo('default', 'default', $connection_info['default']);

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

    // Use the test mail class instead of the default mail handler class.
    variable_set('mail_system', array('default-system' => 'TestingMailSystem'));
    variable_set('file_public_path', 'sites/upal/files');
  }
  
  protected function tearDown() {
    parent::tearDown();
    $time = time();
    $sql = sprintf("SELECT table_name FROM information_schema.tables WHERE table_name LIKE '%s_%%'", $this->prefix);
    $result = db_query($sql);
    foreach ($result as $row) {
      db_query(sprintf("DROP TABLE %s", $row->table_name));
    }
    print 'Drop took:' . (time() - $time) . "sec\n\n";
    $include_path = realpath(
      dirname(__FILE__) . '/../../../../../../../../includes/'
    );
    unlink($include_path . DIRECTORY_SEPARATOR . $this->prefix .'.sql');
    fixture_helper::clear();
  }
  
    public function runCron() {
        $cmd = sprintf(
            '%s cron --root=%s --uri=%s',
            UNISH_DRUSH,
            UPAL_ROOT,
            UPAL_WEB_URL
        );
        // Debug code
//        print "$cmd\n\n";
        exec($cmd, $output, $return);
    }
    
    public function dropCache() {
      $cmd = sprintf(
          '%s cache-clear all --root=%s --uri=%s',
          UNISH_DRUSH,
          UPAL_ROOT,
          UPAL_WEB_URL
      );
      exec($cmd, $output, $return);
    }
}
