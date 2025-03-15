<?php

namespace App\Http\Controllers;

use App\Models\TodoList;
use Illuminate\Http\Request;
use PhpAmqpLib\Message\AMQPMessage;
use Illuminate\Support\Facades\Auth;
use App\Services\RabbitMQ\RabbitmqService;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class TodoListController extends Controller
{
    public function __construct(
        protected RabbitmqService $rabbitmqService,
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

        $this->rabbitmqService->publish([
            'data'  => "hi " . Auth::user()->name . " this is a new todo" . $request->todo,
            'user' => [
                'id' => Auth::user()->id,
                'name' => Auth::user()->name,
                'email' => Auth::user()->email
            ]
        ]);

        return response()->json([
            'message' => "Todo successfully created!"
        ], Response::HTTP_OK);
    }

    public function showQueue()
    {
        $data = $this->rabbitmqService->consumeGet();
        return response()->json([
            'data' => $data,
        ]);
    }
}
