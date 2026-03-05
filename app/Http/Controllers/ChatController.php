<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    public function index()
    {
        $conversations = Conversation::latest()->get();

        return view('chat', [
            'conversations' => $conversations,
            'activeConversation' => null,
            'messages' => [],
        ]);
    }

    public function show(Conversation $conversation)
    {
        $conversations = Conversation::latest()->get();

        return view('chat', [
            'conversations' => $conversations,
            'activeConversation' => $conversation,
            'messages' => $conversation->messages()->oldest()->get(),
        ]);
    }

    public function send(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'conversation_id' => 'nullable|exists:conversations,id',
        ]);

        // Get or create conversation
        if ($request->conversation_id) {
            $conversation = Conversation::findOrFail($request->conversation_id);
        } else {
            $conversation = Conversation::create([
                'title' => Str::limit($request->message, 50),
            ]);
        }

        // Save user message
        $conversation->messages()->create([
            'role' => 'user',
            'content' => $request->message,
        ]);

        // Build messages history for context
        $history = $conversation->messages()->oldest()->get()->map(fn($m) => [
            'role' => $m->role,
            'content' => $m->content,
        ])->toArray();

        // Call Ollama
        $response = Http::timeout(120)->post('http://localhost:11434/api/chat', [
            'model' => 'deepseek-coder',
            'stream' => false,
            'messages' => $history,
        ]);

        $aiContent = $response->json('message.content', '');

        // Save AI response
        $conversation->messages()->create([
            'role' => 'assistant',
            'content' => $aiContent,
        ]);

        return response()->json([
            'conversation_id' => $conversation->id,
            'conversation_title' => $conversation->title,
            'message' => [
                'role' => 'assistant',
                'content' => $aiContent,
            ],
        ]);
    }

    public function destroy(Conversation $conversation)
    {
        $conversation->delete();

        return response()->json(['success' => true]);
    }
}