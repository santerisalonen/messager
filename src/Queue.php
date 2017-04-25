<?php 

namespace Messager;

class Queue {
  
  public static function fetchMsg($count = 10) { 
    $result = Client::getSqs()->receiveMessage(array(
        'QueueUrl' => Client::getQueueUrl(),
        'MaxNumberOfMessages' => $count
    ));
    $events = array();
    if( !$result->get('Messages') ) {
      return false;
    }    
    foreach ($result->get('Messages') as $msg) {
      $events[] = self::parseEvent( $msg ); 
    }
    return $events;
  }
  public static function deleteMsg($handle) { 
    $result = Client::getSqs()->deleteMessage(array(
        'QueueUrl' => Client::getQueueUrl(),
        'ReceiptHandle' => $handle
    ));
    return $result;
  }
  private static function parseEvent($msg) {
    
   
    $body = json_decode($msg['Body'], true);
    $data = json_decode($body['Message'], true);
    $topic = explode(':', $body['TopicArn']);
    $topic = $topic[ count($topic) - 1 ];
    return array(
      'id' => $msg['MessageId'],
      'handle' => $msg['ReceiptHandle'],
      'timestamp' => $body['Timestamp'],
      'event' => new Event($topic, $data)
    );
    
  }
 
}