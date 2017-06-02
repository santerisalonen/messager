<?php
namespace Messager;

class Event {
  
  public function __construct($topic, $data) {
    $this->topic = $topic;
    $this->data = filter_var_array($data, $this->getFilter($this->topic));
  }
  
  public static function publish($topic, $data) {
    $event = new Event($topic, $data);

    $result = Client::getSns()->publish(array(
      'TopicArn' => Client::getTopicArn($event->topic),
      // Message is required
      'Message' => json_encode( $event->data )
    ));
  }  

  
  public function getFilter($topic) {
    switch ( $topic ) {    
      case 'SUPPLIER_CreateItem':
        return array(
          'supplier' => FILTER_SANITIZE_STRING,
          'id' => FILTER_SANITIZE_STRING,
          'qty' => FILTER_VALIDATE_INT,
          'price' => FILTER_VALIDATE_FLOAT,
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