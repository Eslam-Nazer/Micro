<?php

namespace App\Services\RabbitMQ;

use Illuminate\Support\Arr;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitmqService
{
    protected $connection;
    protected $channel;
    protected $queue;
    protected $exchane;
    protected $routingKey = 'my_routing_key';

    public function __construct() {}

    protected function connection()
    {
        $this->connection = new AMQPStreamConnection(
            env('RABBITMQ_HOST', '127.0.0.1'),
            env('RABBITMQ_PORT', '5672'),
            env('RABBITMQ_USER', 'guest'),
            env('RABBITMQ_PASSWORD', 'guest'),
        );
    }

    protected function channel()
    {
        $this->channel = $this->connection->channel();
    }

    protected function generateQueue()
    {
        $this->queue = env('RABBITMQ_QUEUE', 'default');
        return $this->channel->queue_declare($this->queue, false, true, false, false);
    }

    protected function generateExchange(string $exchangeType = 'direct')
    {
        $this->exchane = env('RABBITMQ_EXCHANGE', 'default_exchange');
        $this->channel->exchange_declare($this->exchane, $exchangeType);
    }

    protected function bindQueue()
    {
        $this->channel->queue_bind($this->queue, $this->exchane, $this->routingKey);
    }

    protected function msg(array $data)
    {
        return new AMQPMessage(json_encode($data), ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);
    }

    public function publish(array $data)
    {
        $this->connection();
        $this->channel();
        $this->generateExchange();
        $this->generateQueue();
        $this->bindQueue();
        $this->channel->basic_publish($this->msg($data), $this->exchane, $this->routingKey);
        $this->connectionClose();
    }

    public function consume($callback)
    {
        $this->connection();
        $this->channel();
        $this->generateQueue();
        $this->channel->basic_consume($this->queue, '', false, true, false, false, $callback);

        while ($this->channel->is_consuming()) {
            $this->channel->wait();
        }
        $this->connectionClose();
    }

    public function consumeGet()
    {
        $this->connection();
        $this->channel();
        $this->generateQueue();
        while (true) {
            $msg = $this->channel->basic_get($this->queue);
            if ($msg) {
                $data = json_decode($msg->getBody(), true);
                $msg->ack();
                return $data;
            } else {
                echo "No messages in the queue.\n";
                break;
            }
        }
        $this->connectionClose();
    }

    protected function connectionClose()
    {
        $this->channel->close();
        $this->connection->close();
    }
}
