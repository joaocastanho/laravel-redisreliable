<?php namespace RedisReliableDriver;

use Illuminate\Contracts\Broadcasting\Broadcaster;
use Illuminate\Contracts\Redis\Database as RedisDatabase;

/**
 * @package SocketCluster\SCBroadcaster
 */
class RedisReliableBroadcaster implements Broadcaster
{
    /**
     * The Redis instance.
     *
     * @var \Illuminate\Contracts\Redis\Database
     */
    protected $redis;

    /**
     * The Redis connection to use for broadcasting.
     *
     * @var string
     */
    protected $connection;

    /**
     * Minimum subscribers required to receive the publish, if not matched, add it to a list instead.
     *
     * @var string
     */
    protected $minSubscribers;

    /**
     * Name of the list in Redis in case subscribers number are not matched.
     *
     * @var string
     */
    protected $listName;

    /**
     * Create a new broadcaster instance.
     *
     * @param  \Illuminate\Contracts\Redis\Database $redis
     * @param  string                               $connection
     * @param  integer                              $minSubscribers
     * @param  string                               $listName
     * @return void
     */
    public function __construct(RedisDatabase $redis, $connection = null, $minSubscribers = null, $listName = null)
    {
        $this->redis = $redis;
        $this->connection = $connection;
        $this->minSubscribers = $minSubscribers;
        $this->listName = $listName;
    }

    /**
     * {@inheritdoc}
     */
    public function broadcast(array $channels, $event, array $payload = [])
    {
        $connection = $this->redis->connection($this->connection);

        $payload = json_encode(['event' => $event, 'data' => $payload]);

        foreach ($channels as $channel) {
            $subscribers = $connection->publish($channel, $payload);
            // Add it to a list
            if ($subscribers < $this->minSubscribers) {
                // Add channel
                $payload = json_encode(['channel' => $channel, 'event' => $event, 'data' => $payload]);
                // Add to list name
                $connection->rpush($this->listName, $payload);
            }

        }
    }
}