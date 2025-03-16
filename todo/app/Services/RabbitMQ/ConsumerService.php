<?php

namespace App\Services\RabbitMQ;

use PhpAmqpLib\Message\AMQPMessage;

class ConsumerService extends RabbitmqService
{
    public function consume(string $queue = '')
    {
        $this->connection();
        $this->channel();
        $this->generateQueue($queue);
        $this->channel->basic_consume(
            $this->queue,
            '',
            false,
            false,
            false,
            false,
            function (AMQPMessage $msg) {
                $this->messageData = json_decode($msg->getBody(), true);
                $this->deliveryTag = $msg->getDeliveryTag();
                $msg->ack();
            }
        );

        while ($this->channel->is_consuming()) {
            $this->channel->wait();
            if ($this->deliveryTag != null) {
                break;
            }
        }
        $this->connectionClose();
        return $this->messageData;
    }

    public function consumeGet(string $queue = '')
    {
        $this->connection();
        $this->channel();
        $this->generateQueue($queue);
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
}
