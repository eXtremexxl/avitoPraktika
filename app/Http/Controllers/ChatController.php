<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use App\Models\Chat;
use App\Models\Message;
use App\Models\MessageFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Auth::user()->chats()
            ->with(['ad', 'seller' => function ($query) {
                $query->select('id', 'name', 'avatar');
            }, 'buyer' => function ($query) {
                $query->select('id', 'name', 'avatar');
            }, 'messages'])
            ->withCount(['messages as unread_messages_count' => function ($query) {
                $query->where('sender_id', '!=', auth()->id())
                      ->where('is_read', false);
            }])
            ->where(function ($q) {
                $q->whereNull('deleted_by_user')
                ->orWhereRaw('NOT JSON_CONTAINS(deleted_by_user, ?)', [json_encode((string) auth()->id())]);
            });


        if ($request->has('ad_title') && $request->input('ad_title')) {
            $query->whereHas('ad', function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->input('ad_title') . '%');
            });
        }

        if ($request->has('partner_name') && $request->input('partner_name')) {
            $query->whereHas('seller', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->input('partner_name') . '%');
            })->orWhereHas('buyer', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->input('partner_name') . '%');
            });
        }

        $chats = $query->get();


        $sortDirection = $request->input('sort', 'desc');
        $chats = $sortDirection === 'asc'
            ? $chats->sortBy(function ($chat) {
                $latestMessage = $chat->messages->max('created_at');
                return $latestMessage ?: '1970-01-01 00:00:00';
            })
            : $chats->sortByDesc(function ($chat) {
                $latestMessage = $chat->messages->max('created_at');
                return $latestMessage ?: '1970-01-01 00:00:00';
            });

        return view('chats.index', compact('chats'));
    }

    public function start(Request $request, $adId)
    {
        $ad = Ad::findOrFail($adId);
        
        if ($ad->user_id === Auth::id()) {
            return redirect()->route('ad.show', $ad->id)->with('error', 'Вы не можете начать чат с самим собой');
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
                'deleted_by_user' => [],
            ]);
        }

        return redirect()->route('chat.show', $chat->id);
    }

    public function show($id)
    {
        $chat = Chat::with([
            'messages.sender' => function ($query) {
                $query->select('id', 'name', 'avatar');
            }, 'messages.files', 'ad'
        ])->findOrFail($id);

        if ($chat->seller_id !== Auth::id() && $chat->buyer_id !== Auth::id()) {
            abort(403, 'У вас нет доступа к этому чату');
        }

        if ($chat->isDeletedForCurrentUser()) {
            abort(403, 'Чат удалён для вас');
        }

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
                'files.*' => 'file|max:10240|mimes:jpg,jpeg,png,gif,pdf,doc,docx',
            ], [
                'files.*.mimes' => 'Файл должен быть формата: jpg, jpeg, png, gif, pdf, doc, docx.',
                'files.*.max' => 'Файл не должен превышать 10MB.',
            ]);

            $chat = Chat::findOrFail($chatId);

            if ($chat->seller_id !== Auth::id() && $chat->buyer_id !== Auth::id()) {
                return response()->json(['error' => 'У вас нет доступа к этому чату'], 403);
            }

            if ($chat->isDeletedForCurrentUser()) {
                return response()->json(['error' => 'Чат удалён для вас'], 403);
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
            Log::error('Ошибка отправки сообщения: ' . $e->getMessage());
            return response()->json(['error' => 'Не удалось отправить сообщение'], 500);
        }
    }

    public function delete(Request $request, $chatId)
    {
        try {
            $chat = Chat::with('messages.files')->findOrFail($chatId);

            if ($chat->seller_id !== Auth::id() && $chat->buyer_id !== Auth::id()) {
                return response()->json(['error' => 'У вас нет доступа к этому чату'], 403);
            }


            $deletedByUser = is_array($chat->deleted_by_user) ? $chat->deleted_by_user : [];

            if (!in_array((string) Auth::id(), array_map('strval', $deletedByUser))) {
                $deletedByUser[] = (string) Auth::id();
                $chat->deleted_by_user = $deletedByUser;
                $chat->save();
            }


            if (in_array((string) $chat->seller_id, array_map('strval', $deletedByUser)) &&
                in_array((string) $chat->buyer_id, array_map('strval', $deletedByUser))) {

                foreach ($chat->messages as $message) {
                    foreach ($message->files as $file) {
                        Storage::disk('public')->delete($file->path);
                        $file->delete();
                    }
                }


                $chat->messages()->delete();

                $chat->delete();

                return response()->json(['success' => true, 'message' => 'Чат полностью удалён']);
            }

            return response()->json(['success' => true, 'message' => 'Чат удалён для вас']);
        } catch (\Exception $e) {
            Log::error('Ошибка удаления чата: ' . $e->getMessage(), ['chat_id' => $chatId, 'user_id' => Auth::id()]);
            return response()->json(['error' => 'Не удалось удалить чат: ' . $e->getMessage()], 500);
        }
    }
}