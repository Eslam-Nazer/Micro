<?php

namespace App\Http\Controllers;

use App\Models\TodoList;
use App\Services\RabbitMQ\ConsumerService;
use App\Services\RabbitMQ\PublisherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class TodoListController extends Controller
{
    public function __construct(
        protected PublisherService $publisherService,
        protected ConsumerService $consumerService
    ) {}

    public function index()
    {
        $todo = TodoList::select(
            'id',
            'user_id',
            'todo',
            'created_at',
            'updated_at'
        )->with('users:id,name,email')->get();

        return response()->json([
            'todo' => $todo,
        ], Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        $request->validate([
            'todo'  => ['required', 'string']
        ]);

        TodoList::create([
            'todo'  => $request->todo,
        ]);

        $this->publisherService->publish([
            'data'  => "hi " . Auth::user()->name . " this is a new todo" . $request->todo,
            'user' => [
                'id' => Auth::user()->id,
                'name' => Auth::user()->name,
                'email' => Auth::user()->email
            ]
        ], 'todo_created_queue', 'todo_created_exchange');

        $dataResponse = $this->consumerService->consume('todo_created_queue_success');

        return response()->json([
            'message' => $dataResponse
        ], Response::HTTP_OK);
    }

    public function showQueue()
    {
        // $data = $this->rabbitmqService->consumeGet();
        // return response()->json([
        //     'data' => $data,
        // ]);
    }
}
