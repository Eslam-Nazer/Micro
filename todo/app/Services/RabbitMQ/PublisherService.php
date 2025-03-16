<?php

namespace App\Services\RabbitMQ;

class PublisherService extends RabbitmqService
{
    public function publish(array $data, string $queue = '', string $exchange = '')
    {
        $this->connection();
        $this->channel();
        $this->generateExchange($exchange);
        $this->generateQueue($queue);
        $this->bindQueue();
        $this->channel->basic_publish($this->msg($data), $this->exchange, $this->routingKey);
        $this->connectionClose();
    }
}
