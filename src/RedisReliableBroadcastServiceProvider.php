<?php namespace RedisReliableDriver;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Illuminate\Broadcasting\BroadcastManager;
use Illuminate\Support\Arr;

class RedisReliableBroadcastServiceProvider extends BaseServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Register new BroadcastManager in boot
     *
     * @return void
     */
    public function boot()
    {
        $self = $this;

        $this->app
            ->make(BroadcastManager::class)
            ->extend('redisreliable', function ($app, $config) use ($self) {
                return new RedisReliableBroadcaster(
                    $app->make('redis'), Arr::get($config, 'connection'),  Arr::get($config, 'sub_min'),  Arr::get($config, 'sub_list')
                );
            });
    }
}