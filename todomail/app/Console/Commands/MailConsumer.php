<?php

namespace App\Console\Commands;

use App\Mail\Rabbitmq\TodoMail;
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
    public function handle()
    {
        $consumer = new RabbitmqService();
        $this->info("wait for data.....");
        $data = $consumer->consume();
        if (!$data) {
            $this->error("No data avaliabe");
            return false;
        }
        $todo = $data['data'];
        $name = $data['user']['name'];
        Mail::to('email@test.com')->send(new TodoMail($name, $todo));
        $this->info('mail send successfully!');
    }
}
