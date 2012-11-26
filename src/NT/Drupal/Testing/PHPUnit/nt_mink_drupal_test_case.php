<?php
/**
 *  @copyright The Royal National Theatre
 *  @author John-Paul Drawneek <jdrawneek@nationaltheatre.org.uk>
 */
namespace NT\Drupal\Testing\PHPUnit;

/**
 * Description of bbpa_mink_drupal_test_case
 */
abstract class NtMinkDrupalTestCase extends Behat\Mink\PHPUnit\MinkDrupalTestCase {
    
    protected $base_path = UPAL_WEB_URL;
    
    protected function onNotSuccessfulTest(Exception $e)
    {
      $driver = $this->getSession()->getDriver();
      if ($driver instanceof Behat\Mink\Driver\Selenium2Driver) {
        $imageData = base64_decode($this->getSession()->getDriver()->wdSession->screenshot());
        $path = realpath(UPAL_ROOT . '/../build/');
        file_put_contents($path . $this->prefix . '_image.jpg', $imageData);
      }
        throw $e;
    }

    protected function loadProduction(array $config) {
        $config += array(
            'title'        => '',
            'body'         => '',
            'author'       => '',
            'image'        => '',
            'playwright'   => '',
            'company'      => '',
            'theatre'      => '',
            'originalDate' => '',
            'openingNight' => '',
            'pressNight'   => ''
            
        );
        try {
            $this->getSession()->visit($this->base_path . '/node/add/production');
        } catch (Exception $e) {
            print $e->getMessage() . "\n\n";
            $this->getSession()->visit($this->base_path . '/node/add/production');
        }
        
        
        $page = $this->getSession()->getPage();
        $el = $page->find('css', '#edit-title');
        $el->setValue($config['title']);
        
        $el = $page->find('css', '#edit-body-und-0-value');
        $el->setValue($config['body']);
        
        $el = $page->find('css', '#edit-bbpa-author-und-0-value');
        $el->setValue($config['author']);
        
        $el = $page->find('css', '#edit-bbpa-core-img-und-0-target-id');
        $el->setValue($config['image']);
        
        $el = $page->find('css', '#edit-bbpa-playwright-und-0-target-id');
        $el->setValue($config['playwright']);
        
        $el = $page->find('css', '#edit-bbpa-company-und-0-target-id');
        $el->setValue($config['company']);
        
        $el = $page->find('css', '#edit-bbpa-theatre-und-0-target-id');
        $el->setValue($config['theatre']);
        
        $el = $page->find('css', '#edit-bbpa-original-date-und-0-value-datepicker-popup-0');
        $el->setValue($config['originalDate']);
        
        $el = $page->find('css', '#edit-bbpa-opening-night-und-0-value-datepicker-popup-0');
        $el->setValue($config['openingNight']);
        
        $el = $page->find('css', '#edit-bbpa-press-night-und-0-value-datepicker-popup-0');
        $el->setValue($config['pressNight']);
        
        $el = $page->find('css', '#edit-submit');
        $el->press();

        $this->getSession()->wait(3000); // wait a 3 second
    }

    protected function loadTheatre(array $config) {
        $config += array(
            'title'   => '',
            'address' => ''
        );
        
        try {
            $this->getSession()->visit($this->base_path . '/node/add/theatre');
        } catch (Exception $e) {
            print $e->getMessage() . "\n\n";
            $this->getSession()->visit($this->base_path . '/node/add/theatre');
        }        
        
        $page = $this->getSession()->getPage();
        $el = $page->find('css', '#edit-title');
        $el->setValue($config['title']);
        
        $el = $page->find('css', '#edit-bbpa-address-und-0-value');
        $el->setValue($config['address']);
        
        $el = $page->find('css', '#edit-submit');
        $el->press();

        $this->getSession()->wait(3000); // wait a 3 second
    }

    protected function loadCompany(array $config) {
        $config += array(
            'title' => ''
        );
        try {
            $this->getSession()->visit($this->base_path . '/node/add/company');
        } catch (Exception $e) {
            print $e->getMessage() . "\n\n";
            $this->getSession()->visit($this->base_path . '/node/add/company');
        }
                
        $page = $this->getSession()->getPage();
        $el = $page->find('css', '#edit-title');
        $el->setValue($config['title']);
        
        $el = $page->find('css', '#edit-submit');
        $el->press();

        $this->getSession()->wait(3000); // wait a 3 second
    }


    protected function loadPlaywright(array $config) {
        $config += array(
            'title'              => '',
            'body'               => '',
            'RecordID'           => '',
            'Role RecordID'      => '',
            'Name RecordID'      => '',
            'Person Details'     => '',
            'Main Image'         => '',
            'production history' => array(),
            'biblio fields'      => array(),
            'critical sources'   => array(),
            'articles'           => array(),
            'videos'             => array(),
            'scripts'            => array()
        );
        try {
            $this->getSession()->visit($this->base_path . '/node/add/playwright');
        } catch (Exception $e) {
            print $e->getMessage() . "\n\n";
            $this->getSession()->visit($this->base_path . '/node/add/playwright');
        }
        
        $page = $this->getSession()->getPage();
        $el = $page->find('css', '#edit-title');
        $el->setValue($config['title']);
        
        $el = $page->find('css', '#edit-body-und-0-value');
        $el->setValue($config['body']);
        
        $el = $page->find('css', '#edit-bbpa-recordid-und-0-value');
        $el->setValue($config['RecordID']);
        
        $el = $page->find('css', '#edit-bbpa-role-rid-und-0-value');
        $el->setValue($config['Role RecordID']);
        
        $el = $page->find('css', '#edit-bbpa-name-rid-und-0-value');
        $el->setValue($config['Name RecordID']);
        
        $el = $page->find('css', '#edit-bbpa-pw-person-und-0-target-id');
        $el->setValue($config['Person Details']);
        
        $el = $page->find('css', '#edit-bbpa-pw-image-und-0-target-id');
        $el->setValue($config['Main Image']);
        /**
         * @todo finish this 
         */
//        foreach($config['production history'] AS $i=>$v) {
//            if($i>0) {
//                $el = $page->find('css', '#edit-submit');
//                $el->press();
//                $this->getSession()->wait(3000); // wait a 3 second
//            }
//        }
        
        
        $el = $page->find('css', '#edit-submit');
        $el->press();

        $this->getSession()->wait(3000); // wait a 3 second
    }


    /**
     * #edit-bbpa-item-grouping-und-none, #edit-bbpa-item-grouping-und-fnt-video, #edit-bbpa-item-grouping-und-fnt-also
     * #edit-bbpa-item-grouping-und-browse, #edit-bbpa-item-grouping-und-featured-cont
     * @param array $config 
     */
    protected function loadFeaturedItem(array $config) {
        
        $config += array(
            'title'     => '',
            'body'      => '',
            'item'      => '',
            'grouping'  => '#edit-bbpa-item-grouping-und-none',
            'weighting' => 40
        );
        try {
            $this->getSession()->visit($this->base_path . '/node/add/featured-item');
        } catch (Exception $e) {
            print $e->getMessage() . "\n\n";
            $this->getSession()->visit($this->base_path . '/node/add/featured-item');
        }
        
        $page = $this->getSession()->getPage();
        $el = $page->find('css', '#edit-title');
        $el->setValue($config['title']);
        
        $el = $page->find('css', '#edit-body-und-0-value');
        $el->setValue($config['body']);
        
        $el = $page->find('css', '#edit-bbpa-featured-item-und-0-target-id');
        $el->setValue($config['item']);
        
        $el = $page->find('css', '#edit-bbpa-item-grouping-und');
        $el = $el->find('css', $config['grouping']);
        $el->check();        
        
        $el = $page->find('css', '#edit-bbpa-weight-und-0-value');
        $el->setValue($config['weighting']);
        
        $el = $page->find('css', '#edit-submit');
        $el->press();

        $this->getSession()->wait(3000); // wait a 3 second
    }

    protected function loadArticle(array $config) {
        
        $config += array(
            'title'  => '',
            'body'   => '',
            'image' => '',
            'layout'   => 'bbpa_article_layout_basic'
        );
        try {
            $this->getSession()->visit($this->base_path . '/node/add/bbpa-article');
        } catch (Exception $e) {
            print $e->getMessage() . "\n\n";
            $this->getSession()->visit($this->base_path . '/node/add/bbpa-article');
        }
        
        $page = $this->getSession()->getPage();
        $el = $page->find('css', '#edit-title');
        $el->setValue($config['title']);
        
        $el = $page->find('css', '#edit-body-und-0-value');
        $el->setValue($config['body']);
        
        $el = $page->find('css', '#edit-bbpa-art-image-und-0-target-id');
        $el->setValue($config['image']);
        
        $el = $page->find('css', '#edit-bbpa-art-layout-und-0-value');
        $el->setValue($config['layout']);
        
        $el = $page->find('css', '#edit-submit');
        $el->press();

        $this->getSession()->wait(3000); // wait a 3 second
    }

    protected function loadImage(array $config) {
        
        $config += array(
            'title'  => '',
            'body'   => '',
            'dTitle' => '',
            'file'   => ''
        );        
        try {
            $this->getSession()->visit($this->base_path . '/node/add/image');
        } catch (Exception $e) {
            print $e->getMessage() . "\n\n";
            $this->getSession()->visit($this->base_path . '/node/add/image');
        }
        
        $page = $this->getSession()->getPage();
        $el = $page->find('css', '#edit-title');
        $el->setValue($config['title']);
        
        $el = $page->find('css', '#edit-body-und-0-value');
        $el->setValue($config['body']);
        
        $el = $page->find('css', '#edit-nt-display-title-und-0-value');
        $el->setValue($config['title']);
        
        $el = $page->find('css', '#edit-nt-image-image-und-0-upload');
        $el->setValue($config['file']);
        
        $el = $page->find('css', '#edit-submit');
        $el->press();

        $this->getSession()->wait(3000, "$('.messages.status').length > 0"); // wait a 3 second        
    }

    protected function loadVideo(array $config) {
        
        $config += array(
            'title'        => '',
            'body'         => '',
            'dTitle'       => '',
            'file'         => '',
            'running_time' => '0.33',
            'thumbnail'    => ''
        );
        try {
            $this->getSession()->visit($this->base_path . '/node/add/video');
        } catch (Exception $e) {
            print $e->getMessage() . "\n\n";
            $this->getSession()->visit($this->base_path . '/node/add/video');
        }
        
        $page = $this->getSession()->getPage();
        $el = $page->find('css', '#edit-title');
        $el->setValue($config['title']);
        
        $el = $page->find('css', '#edit-body-und-0-value');
        $el->setValue($config['body']);
        
        $el = $page->find('css', '#edit-nt-display-title-und-0-value');
        $el->setValue($config['dTitle']);
        
        $el = $page->find('css', '#edit-nt-video-file-und-0-upload');
        $el->setValue($config['file']);
        
        $el = $page->find('css', '#edit-nt-video-running-time-und-0-value');
        $el->setValue($config['running_time']);
        
        $el = $page->find('css', '#edit-nt-video-thumb-und-0-target-id');
        $el->setValue($config['thumbnail']);
        
        $el = $page->find('css', '#edit-submit');
        $el->press();

        $this->getSession()->wait(3000); // wait a 3 second
    }


    protected function login() {
        /**
         * @todo Work out why it needs a wake up kick 
         */
//        $this->getSession()->visit($this->base_path . '/user/login');
//        $this->getSession()->wait(1000); // wait a 1 second
        $ch = curl_init($this->base_path . '/user/login');
        $fp = fopen("/dev/null", "w");
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        curl_exec($ch);
        curl_close($ch);
        fclose($fp);

        try {
            $this->getSession()->visit($this->base_path . '/user/login');
        } catch (Exception $e) {
            print $e->getMessage() . "\n\n";
            $this->getSession()->visit($this->base_path . '/user/login');
        }
        
        $page = $this->getSession()->getPage();
        $el = $page->find('css', '#edit-name');
        $el->setValue('admin');
        $el = $page->find('css', '#edit-pass');
        $el->setValue('test1234');
        $el = $page->find('css', '#edit-submit');
        $el->press();
        $this->getSession()->wait(3000); // wait a 3 second
    }
}

