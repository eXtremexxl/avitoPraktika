<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use App\Models\Chat;
use App\Models\Message;
use App\Models\MessageFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Auth::user()->chats()
            ->with(['ad', 'seller', 'buyer', 'messages'])
            ->withCount(['messages as unread_messages_count' => function ($query) {
                $query->where('sender_id', '!=', auth()->id())
                      ->where('is_read', false);
            }]);

        // Фильтры
        if ($request->has('ad_title')) {
            $query->whereHas('ad', function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->input('ad_title') . '%');
            });
        }

        if ($request->has('partner_name')) {
            $query->whereHas('seller', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->input('partner_name') . '%');
            })->orWhereHas('buyer', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->input('partner_name') . '%');
            });
        }

        $chats = $query->get()->sortByDesc(function ($chat) {
            return $chat->messages->max('created_at');
        });

        return view('chats.index', compact('chats'));
    }

    public function start(Request $request, $adId)
    {
        $ad = Ad::findOrFail($adId);
        
        if ($ad->user_id === Auth::id()) {
            return redirect()->route('ad.show', $ad->id)->with('error', 'Вы не можете писать себе');
        }

        $chat = Chat::where('ad_id', $ad->id)
            ->where('seller_id', $ad->user_id)
            ->where('buyer_id', Auth::id())
            ->first();

        if (!$chat) {
            $chat = Chat::create([
                'ad_id' => $ad->id,
                'seller_id' => $ad->user_id,
                'buyer_id' => Auth::id(),
            ]);
        }

        return redirect()->route('chat.show', $chat->id);
    }

    public function show($id)
    {
        $chat = Chat::with(['messages.sender', 'messages.files', 'ad'])->findOrFail($id);

        if ($chat->seller_id !== Auth::id() && $chat->buyer_id !== Auth::id()) {
            abort(403);
        }

        // Отметить сообщения как прочитанные
        $chat->messages()
            ->where('sender_id', '!=', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return view('chats.show', compact('chat'));
    }

    public function sendMessage(Request $request, $chatId)
    {
        try {
            $request->validate([
                'content' => 'required_without:files|string|max:1000',
                'files.*' => 'file|max:10240|mimes:jpg,jpeg,png,gif,pdf,doc,docx', // 10MB max
            ], [
                'files.*.mimes' => 'Файл должен быть формата: jpg, jpeg, png, gif, pdf, doc, docx.',
                'files.*.max' => 'Файл не должен превышать 10MB.',
            ]);

            $chat = Chat::findOrFail($chatId);

            if ($chat->seller_id !== Auth::id() && $chat->buyer_id !== Auth::id()) {
                return response()->json(['error' => 'У вас нет доступа к этому чату'], 403);
            }

            $message = Message::create([
                'chat_id' => $chat->id,
                'sender_id' => Auth::id(),
                'content' => $request->input('content'),
                'is_read' => false,
            ]);

            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    if ($file->isValid()) {
                        $path = $file->store('chat_files', 'public');
                        MessageFile::create([
                            'message_id' => $message->id,
                            'path' => $path,
                            'original_name' => $file->getClientOriginalName(),
                            'mime_type' => $file->getMimeType(),
                            'size' => $file->getSize(),
                        ]);
                    }
                }
            }

            return response()->json(['success' => true, 'message' => 'Сообщение отправлено']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Log::error('Ошибка отправки сообщения: ' . $e->getMessage());
            return response()->json(['error' => 'Не удалось отправить сообщение'], 500);
        }
    }
}