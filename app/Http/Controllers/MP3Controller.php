<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\RabbitMQProducer;

class MP3Controller extends Controller
{
    protected $producer;

    public function __construct(RabbitMQProducer $producer)
    {
        $this->producer = $producer;
    }

    public function index()
    {
        return Inertia::render('MP3/Index');
    }

    public function play(Request $request)
    {
        $data = $request->validate([
            'number' => 'required|integer|min:1|max:100',
            'loket' => 'required|string',
        ]);

        $message = json_encode($data);
        $this->producer->sendMessage($message);

        return response()->json(['status' => 'Message sent to queue']);
    }
}
