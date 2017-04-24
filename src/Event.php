<?php
namespace Messager;

class Event {
  private static $sns;
  private static $topicArnBase;
  
  public function __construct($topic, $data) {
    $this->topic = $topic;
    $this->data = filter_var_array($data, $this->getFilter($this->topic));
  }
  
  public function publish() {
    if( !self::$sns ) {
      self::init();
    }    
    $result = self::$sns->publish(array(
      'TopicArn' => self::$topicArnBase . $this->topic,
      // Message is required
      'Message' => json_encode( $this->data )
    ));
  }  

  private static function init() {
    $conf = json_decode( file_get_contents(BASE_DIR . '/messager.json'), true);
    
    if(!$conf ) {
      throw new \Exception('No configuration found');
    }
    self::$topicArnBase = 'arn:aws:sns:'. $conf['sns']['region'].':'. $conf['aws']['account_id'].':';
    
    $params = $conf['sns'];
    if( \Config::$is_localhost ) {
      $params['profile'] = $conf['aws']['local_profile'];
    }
    self::$sns = new \Aws\Sns\SnsClient($params);
    
  }

  
  public function getFilter($topic) {
    switch ( $topic ) {    
      case 'SUPPLIER_CreateItem':
        return array(
          'supplier' => FILTER_SANITIZE_STRING,
          'id' => FILTER_SANITIZE_STRING,
          'qty' => FILTER_SANITIZE_NUMBER_INT,
          'price' => FILTER_SANITIZE_NUMBER_FLOAT,
          'name' => FILTER_SANITIZE_STRING,
          'ean' => FILTER_SANITIZE_STRING,
          'brand' => FILTER_SANITIZE_STRING,
          'description' => FILTER_SANITIZE_STRING,
          'manufacturer_code' => FILTER_SANITIZE_STRING,
          'category' => FILTER_SANITIZE_STRING,
          'attributes' => array(
            'filter' => FILTER_SANITIZE_STRING,
            'flags'  => FILTER_FORCE_ARRAY
          ),
          'images' => array(
            'filter' => FILTER_VALIDATE_URL,
            'flags'  => FILTER_FORCE_ARRAY
          )
        );
        break;      
      case 'SUPPLIER_UpdateItem':
        return array(
          'supplier' => FILTER_SANITIZE_STRING,
          'id' => FILTER_SANITIZE_STRING,
          'name' => FILTER_SANITIZE_STRING,
          'ean' => FILTER_SANITIZE_STRING,
          'brand' => FILTER_SANITIZE_STRING,
          'description' => FILTER_SANITIZE_STRING,
          'manufacturer_code' => FILTER_SANITIZE_STRING,
          'category' => FILTER_SANITIZE_STRING,
          'attributes' => array(
            'filter' => FILTER_SANITIZE_STRING,
            'flags'  => FILTER_FORCE_ARRAY
          ),
          'images' => array(
            'filter' => FILTER_VALIDATE_URL,
            'flags'  => FILTER_FORCE_ARRAY
          )
        );
        break;
      case 'SUPPLIER_UpdateItemQty':
        return array(
          'supplier' => FILTER_SANITIZE_STRING,
          'id' => FILTER_SANITIZE_STRING,
          'qty' => FILTER_SANITIZE_STRING,
        );
        break;
      case 'SUPPLIER_UpdateItemPrice':
        return array(
          'supplier' => FILTER_SANITIZE_STRING,
          'id' => FILTER_SANITIZE_STRING,
          'qty' => FILTER_SANITIZE_STRING,
        );
        break;  
      case 'INVENTORY_CreateItem':
      case 'INVENTORY_UpdateItem':
        return array(
          'sku' => FILTER_SANITIZE_STRING,
          'name' => FILTER_SANITIZE_STRING,
          'ean' => FILTER_SANITIZE_STRING,
          'brand' => FILTER_SANITIZE_STRING,
          'description' => FILTER_SANITIZE_STRING,
          'manufacturer_code' => FILTER_SANITIZE_STRING,
          'category' => FILTER_SANITIZE_STRING,
          'attributes' => array(
            'filter' => FILTER_SANITIZE_STRING,
            'flags'  => FILTER_FORCE_ARRAY
          ),
          'images' => array(
            'filter' => FILTER_VALIDATE_URL,
            'flags'  => FILTER_FORCE_ARRAY
          )
        );
        break;
      case 'INVENTORY_UpdateQty':
        return array(
          'sku' => FILTER_SANITIZE_STRING,
          'qty' => FILTER_SANITIZE_NUMBER_INT
        );
        break;
      case 'INVENTORY_AddItemSupplier':
        return array(
          'sku' => FILTER_SANITIZE_STRING,
          'supplier' => FILTER_SANITIZE_STRING,
          'supplier_item_id' => FILTER_SANITIZE_STRING,
          'qty' => FILTER_SANITIZE_NUMBER_INT
        );
        break;
      case 'INVENTORY_UpdateSupplierQty':
        return array(
          'sku' => FILTER_SANITIZE_STRING,
          'supplier' => FILTER_SANITIZE_STRING,
          'supplier_item_id' => FILTER_SANITIZE_STRING,
          'qty' => FILTER_SANITIZE_NUMBER_INT
        );
        break;
      default:
        throw new \Exception('Event not registered');
    }
  }
}