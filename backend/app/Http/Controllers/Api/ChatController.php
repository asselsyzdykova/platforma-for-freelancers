<?php

namespace App\Http\Controllers\Api;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index(Request $request, $userId)
    {
        $authUser = $request->user();
        if (! $authUser) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        $authId = $authUser->id;

        $perPage = (int) $request->get('per_page', 20);
        $perPage = $perPage > 0 ? min($perPage, 50) : 20;

        try {
            $paginated = Message::where(function ($q) use ($authId, $userId) {
                $q->where('sender_id', $authId)->where('receiver_id', $userId);
            })->orWhere(function ($q) use ($authId, $userId) {
                $q->where('sender_id', $userId)->where('receiver_id', $authId);
            })->with('sender')->orderBy('created_at', 'desc')->paginate($perPage);

            $messages = collect($paginated->items())->reverse()->values();

            return response()->json([
                'data' => $messages,
                'meta' => [
                    'current_page' => $paginated->currentPage(),
                    'last_page' => $paginated->lastPage(),
                    'per_page' => $paginated->perPage(),
                    'total' => $paginated->total(),
                ],
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            logger()->error('Failed to load messages', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Database error while loading messages. Have you run migrations?'], 500);
        }
    }

    public function conversations(Request $request)
    {
        $authUser = $request->user();
        if (! $authUser) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        $authId = $authUser->id;

        $perPage = (int) $request->get('per_page', 8);
        $perPage = $perPage > 0 ? min($perPage, 50) : 8;
        $page = (int) $request->get('page', 1);
        $page = $page > 0 ? $page : 1;

        try {
            $messages = Message::where('sender_id', $authId)->orWhere('receiver_id', $authId)
                ->with(['sender', 'receiver'])
                ->orderBy('created_at', 'desc')
                ->get();

            $map = [];

            foreach ($messages as $m) {
                $partner = $m->sender_id === $authId ? $m->receiver : $m->sender;
                $partnerId = $partner->id;

                if (!isset($map[$partnerId])) {
                    $map[$partnerId] = [
                        'partner' => [
                            'id' => $partner->id,
                            'name' => $partner->name ?? null,
                            'email' => $partner->email ?? null,
                        ],
                        'last_message' => [
                            'id' => $m->id,
                            'body' => $m->body,
                            'sender_id' => $m->sender_id,
                            'receiver_id' => $m->receiver_id,
                            'created_at' => $m->created_at,
                            'read_at' => $m->read_at,
                        ],
                        'unread_count' => 0,
                    ];
                }

                if ($m->receiver_id === $authId && $m->read_at === null) {
                    $map[$partnerId]['unread_count']++;
                }
            }

            $conversations = array_values($map);

            $total = count($conversations);
            $lastPage = (int) ceil($total / $perPage);
            $offset = ($page - 1) * $perPage;
            $paged = array_slice($conversations, $offset, $perPage);

            return response()->json([
                'data' => $paged,
                'meta' => [
                    'current_page' => $page,
                    'last_page' => $lastPage,
                    'per_page' => $perPage,
                    'total' => $total,
                ],
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            logger()->error('Failed to load conversations', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Database error while loading conversations. Have you run migrations?'], 500);
        }
    }

    public function store(Request $request)
    {
        $authUser = $request->user();
        if (! $authUser) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $data = $request->validate([
            'receiver_id' => 'required|integer|exists:users,id',
            'body' => 'nullable|string|required_without:attachment',
            'attachment' => 'nullable|file|max:10240|mimes:png,jpg,jpeg,gif,webp,pdf,doc,docx,txt',
        ]);

        try {
            $attachmentPath = null;
            $attachmentName = null;
            $attachmentMime = null;
            $attachmentSize = null;

            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $filename = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                $file->storeAs('chat-attachments', $filename, 'public');
                $attachmentPath = $filename;
                $attachmentName = $file->getClientOriginalName();
                $attachmentMime = $file->getClientMimeType();
                $attachmentSize = $file->getSize();
            }

            $message = Message::create([
                'sender_id' => $authUser->id,
                'receiver_id' => $data['receiver_id'],
                'body' => $data['body'] ?? '',
                'attachment_path' => $attachmentPath,
                'attachment_name' => $attachmentName,
                'attachment_mime' => $attachmentMime,
                'attachment_size' => $attachmentSize,
            ]);

            broadcast(new MessageSent($message))->toOthers();

            return response()->json($message->load('sender'));
        } catch (\Illuminate\Database\QueryException $e) {
            logger()->error('Failed to save message', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Database error while saving message. Have you run migrations?'], 500);
        } catch (\Exception $e) {
            logger()->error('Unexpected error saving message', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Server error while saving message. Check server logs.'], 500);
        }
    }

    public function markAsRead(Request $request, $userId)
    {
        $authUser = $request->user();
        if (! $authUser) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        $authId = $authUser->id;

        try {
            $updated = Message::where('sender_id', $userId)
                ->where('receiver_id', $authId)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            return response()->json(['updated' => $updated]);
        } catch (\Illuminate\Database\QueryException $e) {
            logger()->error('Failed to mark messages as read', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Database error while updating messages. Have you run migrations?'], 500);
        }
    }
}
