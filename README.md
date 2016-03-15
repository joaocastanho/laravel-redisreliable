Laravel Redis Reliable Broadcasting Driver
------------------------------------------

Same as RedisBroadcaster from illuminate/broadcasting but allows a customization to ensure at least 1 subscriber has received the event, otherwise adds it into a queue.


Requirements
------------

* laravel >= 5.1

Installation
------------

Using Composer:

```sh
composer require trepatudo/laravel-redisreliable
```

In your config/app.php file add the following provider to your service providers array:

```php
'providers' => [
    ...
    RedisReliableDriver\RedisReliableBroadcastServiceProvider::class,
    ...
]
```

In your config/broadcasting.php file set the default driver to 'socketcluster' and add the connection configuration like so:

```php
'default' => 'redisreliable',

'connections' => [
    ...
    'redisreliable' => [
      'driver' => 'redisreliable',
      'connection' => 'default',
      'sub_min'     => env('BROADCAST_REDISRELIABLE_MIN', 1), // Minimum subscribers required to get the broadcast (pub/sub) 
      'sub_list'   => env('BROADCAST_REDISRELIABLE_LIST', 'laravel_rr_list'), // List to add the broadcast payload and channel in case sub_min was not matched
    ],
    ...
]
```