<?php

namespace Messager;

class Client {
  
  private static $sns;
  private static $sqs;
  private static $conf;
  
  private function __construct() {}
  
  public static function init($file = null, $production = false) {
    if(!$file) {
      $file = 'messager.json';
    }
    $conf = json_decode( file_get_contents($file), true);
    if(!$conf ) {
      throw new \Exception('No configuration found');
    }
    self::$conf = $conf;
    
    
    $sns = $conf['sns'];
    $sqs = $conf['sqs'];
    if( !$production  ) {
      $sns['profile'] = $conf['aws']['local_profile'];
      $sqs['profile'] = $conf['aws']['local_profile'];
    }
    self::$sns = new \Aws\Sns\SnsClient($sns);
    self::$sqs = new \Aws\Sqs\SqsClient($sqs);
  }
  
  public static function getSns() {
    if( !self::$sns ) { self::init(); }
    return self::$sns;
  }
  public static function getSqs() {
    if( !self::$sqs ) { self::init(); }
    return self::$sqs;
  }    
  public static function getTopicArn($topic) {
    return 'arn:aws:sns:' . self::$conf['sns']['region'].':'.self::$conf['aws']['account_id'].':' . $topic;
  }
  public static function getQueueUrl() {
    return 'https://sqs.' . self::$conf['sqs']['region'] . '.amazonaws.com/' . self::$conf['aws']['account_id'] .'/'. self::$conf['sqs']['queue'];
  }
  
}