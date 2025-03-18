<?php

namespace App\Console\Commands;

use App\Mail\Rabbitmq\TodoMail;
use App\Services\RabbitMQ\ConsumerService;
use App\Services\RabbitMQ\PublisherService;
use App\Services\RabbitMQ\RabbitmqService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class MailConsumer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:mail-conumer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'make consumer ready for data';

    /**
     * Execute the console command.
     */
    public function handle(ConsumerService $consumer, PublisherService $publisher)
    {
        $this->info("wait for data.....");
        $data = $consumer->consume('todo_created_queue');
        if (!$data) {
            $this->error("No data avaliabe");
            return false;
        }
        $todo = $data['data'];
        $name = $data['user']['name'];
        Mail::to('email@test.com')->send(new TodoMail($name, $todo));
        $publisher->publish([
            'message' => 'mail send successfully',
            'email' => 'email@test.com',
        ], 'todo_created_queue_success', 'todo_created_exchange');
        $this->info('mail send successfully!');
    }
}
