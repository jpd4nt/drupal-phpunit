<?php
/**
 *  @copyright The Royal National Theatre
 *  @author John-Paul Drawneek <jdrawneek@nationaltheatre.org.uk>
 */
namespace NT\Drupal\Testing\Fixtures;

abstract class fixtures {
  
  public function __construct() {
    $data = $this->data();
    switch (strtolower($this::$type)) {
      case 'nt_image':
        $node = $this->install_nt_image($data);
        break;
      case 'image':
        $node = $this->install_image($data);
        break;
      case 'rich_media':
        $node = $this->install_rich_media($data);
        break;
      case 'video':
        $node = $this->install_video($data);
        break;
      default:
        $node = new \stdClass();
    }
    
    $this::$data = $node;
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
  
  protected function multi_image($data) {
    $output = array();
    foreach ($data as $item) {
      if (array_key_exists('nid', $item)) {
        $output[] = array('nid' => $item['nid']);
      }
      else {
        $output[] = array('nid' => $this->install_image($item));
      }
    }
    return $output;
  }
  
  protected function install_image($data) {
    $data += array(
      'title' =>'image_' . time(),
      'field_display_title' =>'',
      'field_image' =>'',
      'image_title' => '',
      'image_alt' => '',
      'body' =>'',
      'field_credits' =>'',
      'field_tags' => array(),
      'field_production' => NULL,
      'field_archive_code' => NULL,
      'field_asset_id' => NULL,
      'field_asset_category' => array(),
    );
    if (isset($data['file_path'])) {
      $source = new \stdClass();
      $source->uri = $data['file_path'];
      $data['field_image'] = (array) file_copy(
        $source,
        'public://' . drupal_basename($data['file_path'])
      );
    }
    $node = new \stdClass();
    $node->type = 'image';
    $node->language = 'und';
    $node->status = 1;
    $node->name = 'admin';
    $node->uid = 1;
    $node->title = $data['title'];
    $node->field_display_title['und'][0]['value'] = $data['field_display_title'];
    $node->field_image['und'][0] = $data['field_image'];
    $node->field_image['und'][0]['alt'] = $data['image_alt'];
    $node->field_image['und'][0]['title'] = $data['image_title'];
    $node->body['und'][0]['value'] = $data['body'];
    $node->body['und'][0]['format'] = 'full_html';
    $node->field_credits['und'][0]['value'] = $data['field_credits'];
    $node->field_tags['und'] = $data['field_tags'];
    $node->field_production['und'][0]['nid'] = $data['field_production'];
    $node->field_archive_code['und'][0]['value'] = $data['field_archive_code'];
    $node->field_asset_id['und'][0]['value'] = $data['field_asset_id'];
    $node->field_asset_category['und'] = $data['field_asset_category'];
    return $node;
  }
  
  protected function install_nt_image($data) {
    $data += array(
      'title' =>'image_' . time(),
      'nt_display_title' =>'',
      'nt_image_image' =>'',
      'image_title' => '',
      'image_alt' => '',
      'body' =>'',
      'nt_credits' =>'',
      'nt_tags' => array(),
      'nt_image_production' => NULL,
      'nt_archive_code' => NULL,
      'nt_asset_id' => NULL,
      'nt_asset_category' => array(),
    );
    if (isset($data['file_path'])) {
      $source = new \stdClass();
      $source->uri = $data['file_path'];
      $data['nt_image_image'] = (array) file_copy(
        $source,
        'public://' . drupal_basename($data['file_path'])
      );
    }
    $node = new \stdClass();
    $node->type = 'image';
    $node->language = 'und';
    $node->status = 1;
    $node->name = 'admin';
    $node->uid = 1;
    $node->title = $data['title'];
    $node->nt_display_title['und'][0]['value'] = $data['nt_display_title'];
    $node->nt_image_image['und'][0] = $data['nt_image_image'];
    $node->nt_image_image['und'][0]['alt'] = $data['image_alt'];
    $node->nt_image_image['und'][0]['title'] = $data['image_title'];
    $node->body['und'][0]['value'] = $data['body'];
    $node->body['und'][0]['format'] = 'full_html';
    $node->nt_credits['und'][0]['value'] = $data['nt_credits'];
    $node->nt_tags['und'] = $data['nt_tags'];
    $node->nt_image_production['und'][0]['nid'] = $data['nt_image_production'];
    $node->nt_archive_code['und'][0]['value'] = $data['nt_archive_code'];
    $node->nt_asset_id['und'][0]['value'] = $data['nt_asset_id'];
    $node->nt_asset_category['und'] = $data['nt_asset_category'];
    return $node;
  }
  
  protected function install_rich_media($data) {
    $data += array(
      'title' => 'Rich Media_' . time(),
      'field_file_upload' => '',
      'field_file_height' => '',
      'body' => '',
      'field_tags' => '',
      'field_archive_code' => '',
    );
    if (isset($data['file_path'])) {
      $source = new \stdClass();
      $source->uri = $data['file_path'];
      $data['field_file_upload'] = (array) file_copy(
        $source,
        'public://images/' . drupal_basename($data['file_path'])
      );
    }
    $node = new \stdClass();
    $node->type = 'rich_media';
    $node->language = 'und';
    $node->status = 1;
    $node->name = 'admin';
    $node->uid = 1;
    $node->title = $data['title'];
    $node->body['und'][0]['value'] = $data['body'];
    $node->body['und'][0]['format'] = 'full_html';
    $node->field_file_upload['und'][0] = $data['field_file_upload'];
    $node->field_file_height['und'][0]['value'] = $data['field_file_height'];
    $node->field_tags['und'] = $data['field_tags'];
    $node->field_archive_code['und'][0]['value'] = $data['field_archive_code'];
    return $node;
  }

  protected function multi_media_ref($data) {
    $output = array();
//    var_dump($data);
    foreach ($data as $item) {
      if (isset($item['nid'])) {
        $output[] = array('nid' => $item['nid']);
      }
      else {
        $output[] = array('nid' => $this->install_media_ref($item));
      }
    }
    
    return $output;
  }

  protected function install_media_ref($data) {
    $type = array_shift(array_keys($data));
    $id = NULL;
    switch ($type) {
      case 'image':
        $id = $this->install_image($data[$type]);
        break;
      case 'rich_media':
        $id = $this->install_rich_media($data[$type]);
        break;
      case 'video':
        $id = $this->install_video($data[$type]);
        break;
      case 'gallery':
        $id = $this->install_gallery($data[$type]);
        break;
    }
    return $id;
  }

  protected function multi_video($data) {
    $output = array();
//    var_dump($data);
    foreach ($data as $item) {
      if (isset($item['nid'])) {
        $output[] = array('nid' => $item['nid']);
      }
      else {
        $output[] = array('nid' => $this->install_video($item));
      }
    }
    
    return $output;
  }
  
  protected function install_video($data) {
    $data += array(
      'title' => 'Video_' . time(),
      'field_display_title' => '',
      'field_file_url' => '',
      'field_running_time' => NULL,
      'body' => '',
      'field_thumb' => NULL,
      'field_srt' => NULL,
      'field_credits' => '',
      'field_asset_category' => NULL,
      'field_backstage' => NULL,
      'field_tags' => array(),
      'field_archive_code' => NULL,
      'field_asset_id' => NULL,
      'field_project_title' => '',
      'field_weighting' => 1,
    );
    
    if (is_array($data['field_thumb'])) {
      $data['field_thumb'] = $this->multi_image($data['field_thumb']);
    }
    
    $node = new \stdClass();
    $node->type = 'video';
    $node->language = 'und';
    $node->status = 1;
    $node->name = 'admin';
    $node->uid = 1;
    
    $node->title = $data['title'];
    $node->body['und'][0]['value'] = $data['body'];
    $node->body['und'][0]['format'] = 'full_html';
    $node->field_display_title['und'][0]['value'] = $data['field_display_title'];
    $node->field_file_url['und'][0]['value'] = $data['field_file_url'];
    if (isset($data['field_running_time'])) {
      $node->field_running_time['und'][0]['value'] = $data['field_running_time'];
    }
    if (isset($data['field_thumb'])) {
      $node->field_thumb['und'] = $data['field_thumb'];
    }
    if (isset($data['field_srt'])) {
      $node->field_srt['und'][0] = $data['field_srt'];
    }
    $node->field_credits['und'][0]['value'] = $data['field_credits'];
    if (isset($data['field_asset_category'])) {
      $node->field_asset_category['und'][0]['tid'] = $data['field_asset_category'];
    }    
    if (isset($data['field_backstage'])) {
      foreach ($data['field_backstage'] as $delta => $value) {
        $node->field_backstage['und'][$delta]['tid'] = $value;
      }
    }
    if (!empty($data['field_tags'])) {
      $node->field_tags['und'] = $data['field_tags'];
    }
    $node->field_archive_code['und'][0]['value'] = $data['field_archive_code'];
    $node->field_asset_id['und'][0]['value'] = $data['field_asset_id'];
    $node->field_project_title['und'][0]['value'] = $data['field_project_title'];
    $node->field_weighting['und'][0]['value'] = $data['field_weighting'];
    
    return $node;
  }
}
