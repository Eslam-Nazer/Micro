<?php

namespace App\Services\RabbitMQ;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use PhpAmqpLib\Wire\AMQPTable;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class RabbitmqService
{
    protected $connection;
    protected $channel;
    protected $queue;
    protected $exchange;
    protected $routingKey;
    protected $messageData;

    protected $deliveryTag;

    public function __construct()
    {
        $this->routingKey = $this->generateRoutingKey();
    }

    protected function generateRoutingKey(): string
    {
        return 'route_' . Str::uuid();
    }

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

    protected function generateQueue(string $queue = '')
    {
        $this->queue = $queue ?? env('RABBITMQ_QUEUE', 'default');
        return $this->channel->queue_declare($this->queue, false, true, false, false);
    }

    protected function generateExchange(string $exchange = '', string $exchangeType = 'direct')
    {
        $this->exchange = $exchange ?? env('RABBITMQ_EXCHANGE', 'default_exchange');
        $this->channel->exchange_declare($this->exchange, $exchangeType);
    }

    protected function bindQueue()
    {
        $this->channel->queue_bind($this->queue, $this->exchange, $this->routingKey);
    }

    protected function msg(array $data)
    {
        $data['routing_key'] = $this->routingKey;
        $properties = [
            'content_type' => 'application/json',
            'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
            'application_headers' => new AMQPTable([
                'custom_delivery_tag' => uniqid(),
                'routing_key' => $this->routingKey,
            ])
        ];
        return new AMQPMessage(json_encode($data), $properties);
    }

    protected function connectionClose()
    {
        $this->channel->close();
        $this->connection->close();
    }
}
