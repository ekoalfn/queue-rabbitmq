<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use App\Events\AudioPlayed;
use Illuminate\Support\Facades\Log;

class RabbitMQConsumer extends Command
{
    protected $signature = 'rabbitmq:consume';
    protected $description = 'Consume messages from RabbitMQ';

    public function handle()
    {
        $queue = config('rabbitmq.queue');
        $connection = new AMQPStreamConnection(
            config('rabbitmq.host'),
            config('rabbitmq.port'),
            config('rabbitmq.user'),
            config('rabbitmq.password')
        );
        $channel = $connection->channel();
        $channel->queue_declare($queue, false, false, false, false);

        $callback = function (AMQPMessage $msg) {
            $message = json_decode($msg->body, true);
            // Log the received message
            Log::info('Received message from RabbitMQ', ['message' => $message]);
            
            // Dispatch event
            event(new AudioPlayed($message));

            // Log the event dispatch
            Log::info('Dispatched AudioPlayed event', ['message' => $message]);
        };

        $channel->basic_consume($queue, '', false, true, false, false, $callback);

        while ($channel->is_consuming()) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }
}
