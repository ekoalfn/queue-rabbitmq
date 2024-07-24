<?php

namespace App\Services;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Log;

class RabbitMQProducer
{
    protected $connection;
    protected $channel;
    protected $queue;

    public function __construct()
    {
        $this->queue = config('rabbitmq.queue');
        $this->connection = new AMQPStreamConnection(
            config('rabbitmq.host'),
            config('rabbitmq.port'),
            config('rabbitmq.user'),
            config('rabbitmq.password')
        );
        $this->channel = $this->connection->channel();
        $this->channel->queue_declare($this->queue, false, false, false, false);
    }

    public function sendMessage($message)
    {
        Log::info('Sending message to RabbitMQ: ', ['message' => $message]);
        $msg = new AMQPMessage($message);
        $this->channel->basic_publish($msg, '', $this->queue);
    }

    public function __destruct()
    {
        $this->channel->close();
        $this->connection->close();
    }
}
