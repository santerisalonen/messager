# Messager

## Installation

Add to composer.json

```javascript
"require": { "phoesis/messager": "dev-master" },
"repositories": [ {
        "type": "git",
        "url":  "https://github.com/santerisalonen/messager.git"
    }
]
```

## Configuration 

Copy messager-example.json to app root. Init 

```php
Messager\Client::init('path/to/messager.json')
```

## Usage
### Send event

```php
$arr = array( 'field' => 'value, 'field2' => 'value2');
$topic = 'INVENTORY_CreateItem';
Messager\Event::publish($topic, $arr);
```

### Read events from queue

```php
$messages = Messager\Queue::fetchMsg(10);

foreach($messages as $msg) {
  $ts = $msg['timestamp'];
  $event = $msg['event'];
  // do something with event 
  // ...
  // delete message
  Messager\Queue::deleteMsg($msg['handle']);
}

```



