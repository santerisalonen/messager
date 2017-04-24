<?php 

namespace Messager;

class Queue {
  private static $sqs;
  private static $queueUrl;
  public static function fetchMsg($count = 10) { 
  
    if( !self::$sqs ) { self::init(); }
    $result = self::$sqs->receiveMessage(array(
        'QueueUrl' => self::$queueUrl,
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
    
    if( !self::$sqs ) { self::init(); }
    
    $result = self::$sqs->deleteMessage(array(
        'QueueUrl' => self::$queueUrl,
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
  private static function init() {
    $conf = json_decode( file_get_contents(BASE_DIR . '/messager.json'), true);
    if(!$conf ) {
      throw new \Exception('No configuration found');
    } 
    self::$queueUrl = 'https://sqs.' . $conf['sqs']['region'] . '.amazonaws.com/' . $conf['aws']['account_id'] .'/'. $conf['sqs']['queue'];
    
    $params = $conf['sqs'];
    if( \Config::$is_localhost ) {
      $params['profile'] = $conf['aws']['local_profile'];
    }
    self::$sqs = new \Aws\Sqs\SqsClient($params);
  }
}